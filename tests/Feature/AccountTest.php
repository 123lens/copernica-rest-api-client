<?php
namespace Budgetlens\CopernicaRestApi\Tests\Feature\Endpoints;

use Budgetlens\CopernicaRestApi\Resources\Account\Consumption;
use Budgetlens\CopernicaRestApi\Resources\Account\Identity;
use Budgetlens\CopernicaRestApi\Tests\TestCase;

class AccountTest extends TestCase
{
    /** @test */
    public function getIdentity(): void
    {
        $this->useMock('200-get-identity.json');

        $identity = $this->client->account->identity();
        $this->assertInstanceOf(Identity::class, $identity);
        $this->assertSame('1', $identity->id);
        $this->assertSame('unit test', $identity->name);
        $this->assertSame('Description', $identity->description);
        $this->assertSame('PHP Unit Test', $identity->company);
    }

    /** @test */
    public function getConsumption(): void
    {
        $this->useMock('200-get-consumption.json');

        $consumption = $this->client->account->consumption(new \DateTime('first day of this month'));

        $this->assertInstanceOf(Consumption::class, $consumption);
        $this->assertSame(1, $consumption->emails);
        $this->assertSame(2, $consumption->texts);
        $this->assertSame(3, $consumption->fax);
        $this->assertSame(4, $consumption->webpages);
        $this->assertSame(5, $consumption->apicalls);
        $this->assertSame(6, $consumption->litmustests);
        $this->assertSame(7, $consumption->twofiftyoktests);
    }
}
