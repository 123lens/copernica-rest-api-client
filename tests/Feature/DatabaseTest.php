<?php
namespace Budgetlens\CopernicaRestApi\Tests\Feature\Endpoints;

use Budgetlens\CopernicaRestApi\Enum\FieldType;
use Budgetlens\CopernicaRestApi\Resources\Account\Consumption;
use Budgetlens\CopernicaRestApi\Resources\Account\Identity;
use Budgetlens\CopernicaRestApi\Resources\Database;
use Budgetlens\CopernicaRestApi\Resources\PaginatedResult;
use Budgetlens\CopernicaRestApi\Tests\TestCase;
use Illuminate\Support\Collection;

class DatabaseTest extends TestCase
{
    /** @test */
    public function listDatabases(): void
    {
        $this->useMock('200-get-list-databases.json');

        $databases = $this->client->database->list();
        $this->assertInstanceOf(PaginatedResult::class, $databases);
        $this->assertIsInt($databases->start);
        $this->assertIsInt($databases->limit);
        $this->assertIsInt($databases->count);
        $this->assertInstanceOf(Collection::class, $databases->data);
        $this->assertCount(2, $databases->data);
        $database = $databases->data->first();
        $this->assertInstanceOf(Database::class, $database);
        $this->assertIsInt($database->ID);
        $this->assertSame(1, $database->ID);
        $this->assertSame('PHPUnit_Test_Archived', $database->name);
        $this->assertSame('Database 1', $database->description);
        $this->assertTrue($database->archived);
        $this->assertInstanceOf(\DateTime::class, $database->created);
        $this->assertInstanceOf(PaginatedResult::class, $database->fields);
        $this->assertInstanceOf(Collection::class, $database->fields->data);
        $this->assertInstanceOf(Database\Intentions::class, $database->intentions);
        $this->assertCount(2, $database->fields->data);
        $this->assertInstanceOf(Database\Field::class, $database->fields->data->first());
    }

    /** @test */
    public function getDatabaseDetails()
    {
        $this->useMock('200-get-database-details.json');
        $database = $this->client->database->get(1);
        $this->assertInstanceOf(Database::class, $database);
        $this->assertIsInt($database->ID);
        $this->assertSame(1, $database->ID);
        $this->assertSame('PHPUnit_Test_Archived', $database->name);
        $this->assertSame('Database 1', $database->description);
        $this->assertTrue($database->archived);
        $this->assertInstanceOf(\DateTime::class, $database->created);
        $this->assertInstanceOf(PaginatedResult::class, $database->fields);
        $this->assertInstanceOf(Collection::class, $database->fields->data);
        $this->assertInstanceOf(Database\Intentions::class, $database->intentions);
        $this->assertCount(2, $database->fields->data);
        $this->assertInstanceOf(Database\Field::class, $database->fields->data->first());
    }

    /** @test */
    public function canCreateDatabase()
    {
        $this->useMock(null, 201, ['X-Created' => ['100']]);

        $database = $this->client->database->create('phpunit test rest', 'Test database');
        $this->assertInstanceOf(Database::class, $database);
        $this->assertSame(100, $database->ID);
        $this->assertSame('phpunit_test_rest', $database->name);
        $this->assertSame('Test database', $database->description);
    }

    /** @test */
    public function canCopyDatabase()
    {
        $this->useMock(null, 201, ['X-Created' => ['100']]);
        $database = $this->client->database->copy(1,'phpunit test rest copy');
        $this->assertInstanceOf(Database::class, $database);
        $this->assertSame(100, $database->ID);
        $this->assertSame('phpunit_test_rest_copy', $database->name);
    }

    /** @test */
    public function canUpdateDatabase()
    {
        $this->useMock(null, 204);
        $id = -1;
        $result = $this->client->database->update($id, 'updated name asd', 'some description');
        $this->assertTrue($result);
    }

    /** @test */
    public function canRetrieveUnsubscribeBehaviourDelete()
    {
        $this->useMock('200-get-unsubscribe-behaviour-delete.json');
        $id = 1;
        $response = $this->client->database->getUnsubscribeBehaviour($id);
        $this->assertInstanceOf(Database\UnsubscribeBehaviour::class, $response);
        $this->assertSame('remove', $response->behavior);
    }

    /** @test */
    public function canRetrieveUnsubscribeBehaviourUpdate()
    {
        $this->useMock('200-get-unsubscribe-behaviour-update.json');
        $id = 1;
        $response = $this->client->database->getUnsubscribeBehaviour($id);
        $this->assertInstanceOf(Database\UnsubscribeBehaviour::class, $response);
        $this->assertSame('update', $response->behavior);
        $this->assertInstanceOf(Collection::class, $response->fields);
        $this->assertSame('0', $response->fields->get('newsletter'));
        $this->assertSame('no', $response->fields->get('optin_common'));
        $this->assertSame('no', $response->fields->get('optin_birthday'));
        $this->assertSame('no', $response->fields->get('optin_service'));
        $this->assertSame('no', $response->fields->get('optin_product'));
    }

    /** @test */
    public function canUpdateUnsubscribeBehaviour()
    {
        $this->useMock(null, 204);
        $id = -1;
        $response = $this->client->database->updateUnsubscribeBehaviour($id, 'remove');
        $this->assertTrue($response);
    }

    /** @test */
    public function canListSelections()
    {
        $this->useMock('200-get-database-selections.json');
        $id = 1;
        $response = $this->client->database->getSelections($id);
        $this->assertInstanceOf(PaginatedResult::class, $response);
        $this->assertCount(2, $response->data);
        $this->assertInstanceOf(Database\Selection::class, $response->data->first());
        $this->assertSame(2, $response->data->first()->ID);
        $this->assertSame('Selection1', $response->data->first()->name);
        $this->assertSame('description 1', $response->data->first()->description);
        $this->assertSame(1, $response->data->first()->parentId);
        $this->assertFalse($response->data->first()->hasChildren);
        $this->assertFalse($response->data->first()->hasReferred);
        $this->assertTrue($response->data->first()->hasRules);
        $this->assertInstanceOf(\DateTime::class, $response->data->first()->lastBuilt);
        $this->assertInstanceOf(Database\Intentions::class, $response->data->first()->intentions);
    }

    /** @test */
    public function canCreateSelection()
    {
        $this->useMock(null, 201, ['X-Created' => ['100']]);
        $response = $this->client->database->createSelection(1, 'selection name', 'unit test');
        $this->assertInstanceOf(Database\Selection::class, $response);
        $this->assertSame(100, $response->ID);
        $this->assertSame('selection_name', $response->name);
        $this->assertSame('unit test', $response->description);
    }

    /** @test */
    public function canListCollections()
    {
        $this->useMock('200-get-database-collections.json');
        $id = 1;
        $response = $this->client->database->getCollections($id);
        $this->assertInstanceOf(PaginatedResult::class, $response);
        $this->assertCount(2, $response->data);
        $this->assertInstanceOf(Database\Collection::class, $response->data->first());
        $this->assertSame(1, $response->data->first()->ID);
        $this->assertSame('Collection 1', $response->data->first()->name);
        $this->assertSame(1, $response->data->first()->database);
        $this->assertInstanceOf(PaginatedResult::class, $response->data->first()->fields);
        $this->assertCount(2, $response->data->first()->fields->data);
        $this->assertInstanceOf(Database\Field::class, $response->data->first()->fields->data->first());
        $this->assertSame(1, $response->data->first()->fields->data->first()->ID);
        $this->assertInstanceOf(Database\Intentions::class, $response->data->first()->intentions);
    }

    /** @test */
    public function canCreateCollection()
    {
        $this->useMock(null, 201, ['X-Created' => ['100']]);
        $id = 1;
        $response = $this->client->database->createCollection($id, 'collection name');
        $this->assertInstanceOf(Database\Collection::class, $response);
        $this->assertSame(100, $response->ID);
        $this->assertSame('collection_name', $response->name);
    }

    /** @test */
    public function canListFields()
    {
        $this->useMock('200-get-database-fields.json');

        $id = 1;
        $response = $this->client->database->getFields($id);
        $this->assertInstanceOf(PaginatedResult::class, $response);
        $this->assertCount(3, $response->data);
        $this->assertInstanceOf(Database\Field::class, $response->data->first());
        $this->assertSame(1, $response->data->first()->ID);
        $this->assertSame('field1', $response->data->first()->name);
        $this->assertSame('integer', $response->data->first()->type);
        $this->assertSame("0", $response->data->first()->value);
        $this->assertFalse($response->data->first()->displayed);
        $this->assertFalse($response->data->first()->ordered);
        $this->assertSame(50, $response->data->first()->length);
        $this->assertSame(1, $response->data->first()->textlines);
        $this->assertFalse($response->data->first()->hidden);
        $this->assertTrue($response->data->first()->index);
    }

    /** @test */
    public function canCreateTextField()
    {
        $this->useMock(null, 201, ['X-Created' => ['100']]);
        $id = 1;
        $response = $this->client->database->createField(
            $id,
            'field name',
            FieldType::TEXT
        );
        $this->assertInstanceOf(Database\Field::class, $response);
        $this->assertSame(100, $response->ID);
    }


    /** @test */
    public function canCreateSelectField()
    {
        $this->useMock(null, 201, ['X-Created' => ['100']]);
        $id = 1;
        $response = $this->client->database->createField(
            $id,
            'selectable field',
            FieldType::SELECT,
            [
                'option1',
                'option2',
                'option3'
            ]
        );
        $this->assertInstanceOf(Database\Field::class, $response);
        $this->assertSame(100, $response->ID);
    }

    /** @test */
    public function canUpdateField()
    {
        $this->useMock(null, 204);

        $databaseId = 1;
        $id = 1;
        $response = $this->client->database->updateField(
            databaseId: $databaseId,
            id: $id,
            name: 'new name',
            length: 25
        );
        $this->assertTrue($response);
    }

    /** @test */
    public function canDeleteField()
    {
        $this->useMock(null, 204);
        $databaseId = -1;
        $id = -1;

        $response = $this->client->database->deleteField($databaseId, $id);
        $this->assertTrue($response);
    }

    /** @test */
    public function canListInterests()
    {
        $this->useMock('200-get-database-interests.json');
        $id = 1;
        $response = $this->client->database->getInterests($id);

        $this->assertInstanceOf(PaginatedResult::class, $response);
        $this->assertCount(4, $response->data);
        $this->assertInstanceOf(Database\Interest::class, $response->data->first());
        $this->assertSame(1, $response->data->first()->ID);
        $this->assertSame('optin_common', $response->data->first()->name);
        $this->assertSame('Newsletter', $response->data->first()->group);
    }

    /** @test */
    public function canCreateInterest()
    {
        $this->useMock(null, 201, ['X-Created' => ['100']]);
        $id = 1;
        $response = $this->client->database->createInterest($id, 'interest name', 'group name');
        $this->assertInstanceOf(Database\Interest::class, $response);
        $this->assertSame(100, $response->ID);
        $this->assertSame('interest_name', $response->name);
        $this->assertSame('group name', $response->group);
    }

    /** @test */
    public function canUpdateInterest()
    {
        $this->useMock(null, 204);

        $id = 1;
        $response = $this->client->interest->update(
            id: $id,
            group: 'new group name',
        );
        $this->assertTrue($response);
    }

    /** @test */
    public function canDeleteInterest()
    {
        $this->useMock(null, 204);

        $id = 1;
        $response = $this->client->interest->delete(
            id: $id,
        );
        $this->assertTrue($response);
    }


}
