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

    /** @test */
    public function canUpdateIntensions()
    {
        $this->markTestSkipped('Response from Copernica is invalid, till thet fix this response mark skipped');
        $this->useMock(null, 204);
        $id = 1;
        $result = $this->client->database->updateIntentions($id, email: true);
        $this->assertTrue($result);
    }
}