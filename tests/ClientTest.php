<?php
namespace Budgetlens\CopernicaRestApi\Tests;

use Budgetlens\CopernicaRestApi\Client;
use Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException;

class ClientTest extends TestCase
{
    /** @test */
    public function performingAnHttpCallWithoutSettingAnAccessTokenThrowsAnException()
    {
        $this->expectException(CopernicaApiException::class);
        $client = new Client('');
        $client->performHttpCall('GET', 'non-existing-resource');
    }
}
