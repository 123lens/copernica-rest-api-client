<?php
namespace Budgetlens\CopernicaRestApi\Tests\Feature\Endpoints;

use Budgetlens\CopernicaRestApi\Enum\FieldType;
use Budgetlens\CopernicaRestApi\Exceptions\FilterUnknownOperatorException;
use Budgetlens\CopernicaRestApi\Resources\Database;
use Budgetlens\CopernicaRestApi\Resources\PaginatedResult;
use Budgetlens\CopernicaRestApi\Resources\Profile;
use Budgetlens\CopernicaRestApi\Support\FieldFilter;
use Budgetlens\CopernicaRestApi\Tests\TestCase;
use Illuminate\Support\Collection;

class ProfileTest extends TestCase
{
    /** @test */
    public function canListProfiles()
    {
        $this->useMock('200-get-database-profiles.json');
        $databaseId = 1;
        $response = $this->client->profile->list($databaseId);
        $this->assertInstanceOf(PaginatedResult::class, $response);
        $this->assertInstanceOf(Profile::class, $response->data->first());
        $this->assertSame(1, $response->data->first()->ID);
        $this->assertInstanceOf(Collection::class, $response->data->first()->fields);
        $this->assertSame('field1', $response->data->first()->fields->get('field1'));
        $this->assertSame('field2', $response->data->first()->fields->get('field2'));
        $this->assertInstanceOf(Collection::class, $response->data->first()->interests);
        $this->assertInstanceOf(Database\Interest::class, $response->data->first()->interests->first());
        $this->assertSame(1, $response->data->first()->database);
        $this->assertSame('secret1', $response->data->first()->secret);
        $this->assertInstanceOf(\DateTime::class, $response->data->first()->created);
        $this->assertInstanceOf(\DateTime::class, $response->data->first()->modified);
        $this->assertFalse($response->data->first()->removed);
    }

    /** @test */
    public function canListProfilesFilterd()
    {
        $this->useMock('200-get-database-profiles-filterd.json');
        $databaseId = 1;
        // build filters.
        $filter = new FieldFilter();
        $filter->add('sex', 'M');
        $filter->like('surname', 'jans');
        $filter->like('zipcode', '1000', 'after');
        $response = $this->client->profile->list($databaseId, fields: $filter);
        $this->assertInstanceOf(PaginatedResult::class, $response);
        $this->assertInstanceOf(Profile::class, $response->data->first());
        $this->assertSame(1, $response->data->first()->ID);
        $this->assertInstanceOf(Collection::class, $response->data->first()->fields);
        $this->assertSame('M', $response->data->first()->fields->get('sex'));
        $this->assertSame('jansen', $response->data->first()->fields->get('surname'));
        $this->assertSame('1000AA', $response->data->first()->fields->get('zipcode'));
        $this->assertSame('City', $response->data->first()->fields->get('city'));
        $this->assertInstanceOf(Collection::class, $response->data->first()->interests);
        $this->assertSame(1, $response->data->first()->database);
        $this->assertSame('secret1', $response->data->first()->secret);
        $this->assertInstanceOf(\DateTime::class, $response->data->first()->created);
        $this->assertInstanceOf(\DateTime::class, $response->data->first()->modified);
        $this->assertFalse($response->data->first()->removed);
    }

    /** @test */
    public function unknownFilterOperatorThrowsException()
    {
        $this->expectException(FilterUnknownOperatorException::class);
        $this->expectExceptionMessage('!==');
        $id = 1;
        // build filters.
        $filter = new FieldFilter();
        $filter->add('sex', 'M', '!==');
    }

    /** @test */
    public function canCreateProfile()
    {
        $this->useMock(null, 201, ['X-Created' => ['100']]);
        $id = 1;
        $fields = [
            'firstname' => 'unit',
            'surname' => 'test'
        ];
        $interests = [
            'new_name'
        ];
        $response = $this->client->profile->create($id, $fields, $interests);
        $this->assertInstanceOf(Profile::class, $response);
        $this->assertSame(100, $response->ID);
        $this->assertSame('unit', $response->fields->get('firstname'));
        $this->assertSame('test', $response->fields->get('surname'));
    }

    /** @test */
    public function canUpdateProfile()
    {
        $this->useMock(null, 204);

        $databaseId = 1;
        $id = 1;
        $fields = [
            'firstname' => 'unit',
            'surname' => 'test'
        ];
        $interests = [
            'new_name'
        ];

        $response = $this->client->profile->update(
            $databaseId,
            $id,
            $fields,
            $interests
        );
        $this->assertIsNumeric($response);
    }

    /** @test */
    public function canUpdateOrCreateProfile()
    {
        $this->useMock(null, 204);

        $databaseId = 1;
        $id = 1;
        $fields = [
            'firstname' => 'unit',
            'surname' => 'test'
        ];

        $response = $this->client->profile->updateOrCreate(
            $databaseId,
            $id,
            $fields,
        );
        $this->assertIsNumeric($response);
    }

    /** @test */
    public function canDeleteProfiles()
    {
        $this->useMock(null, 204);

        $databaseId = 1;
        $ids = [
            1,
            2,
            3,
        ];

        $response = $this->client->profile->delete($databaseId, $ids);
        $this->assertTrue($response);
    }
}