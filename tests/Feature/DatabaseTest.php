<?php
namespace Budgetlens\CopernicaRestApi\Tests\Feature\Endpoints;

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
}
