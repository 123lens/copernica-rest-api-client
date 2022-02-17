<?php
namespace Budgetlens\CopernicaRestApi\Tests;

use Budgetlens\CopernicaRestApi\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected $defaultResponseHeader = [
        'Content-Type' => [
            'application/json; charset=utf-8'
        ]
    ];

    /**
     * @var Client
     */
    protected $client;

    protected function setUp(): void
    {
        $this->client = new Client(getenv('ACCESS_TOKEN'));

        parent::setUp();
    }

    public function getMockfile(string $filename): ?string
    {
        $file = __DIR__ . "/Mocks/{$filename}";
        if (file_exists($file)) {
            return file_get_contents($file);
        }
        throw new \Exception("Mockfile not found '{$filename}'");
    }


    protected function useMock($file = null, $status = 200, $header = null)
    {
        // set mock client
        $mockHandler = new MockHandler();
        $client = new \GuzzleHttp\Client([
            'handler' => $mockHandler
        ]);
        $mockHandler->append(new Response(
            $status,
            $header ?? $this->defaultResponseHeader,
            !is_null($file) ? $this->getMockfile($file) : null
        ));
        $this->client->setClient($client);
    }
}