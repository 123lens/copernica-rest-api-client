<?php
namespace Budgetlens\CopernicaRestApi\Tests\Feature;

use Budgetlens\CopernicaRestApi\Client;
use Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException;
use Budgetlens\CopernicaRestApi\Tests\TestCase;

class ClientTest extends TestCase
{
    /** @test */
    public function performingAnHttpCallWithoutSettingAnAccessTokenThrowsAnException()
    {
        $this->expectException(CopernicaApiException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage('Error executing API call : Copernica REST API Error 400: Invalid access token');

        $client = new Client('');
        $client->account->identity();
    }
}
