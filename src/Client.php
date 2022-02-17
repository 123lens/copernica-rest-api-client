<?php
namespace Budgetlens\CopernicaRestApi;

use Budgetlens\CopernicaRestApi\Contracts\Config;
use Budgetlens\CopernicaRestApi\Endpoints\Account;
use Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException;
use Composer\CaBundle\CaBundle;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

class Client
{
    const HTTP_STATUS_NO_CONTENT = 204;

    const USER_AGENT = "Budgetlens/CopernicaRestApi/V3.0.0";

    /** @var Config  */
    protected $config;

    /** @var \GuzzleHttp\Client */
    protected $httpClient;

    /** @var string */
    private $access_token;

    /** @var Account */
    public $account;


    public function __construct(string $accessToken)
    {
        $this->access_token = $accessToken;

        // initialize available endpoints
        $this->initializeEndpoints();
    }

    /**
     * Get Config
     * @return Config
     */
    public function getConfig(): Config
    {
        if (is_null($this->config)) {
            $this->config = new ApiConfig();
        }

        return $this->config;
    }

    /**
     * Set (custom) config
     * @param Config $config
     * @return $this
     */
    public function setConfig(Config $config): self
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Initialize available endpoints
     */
    public function initializeEndpoints(): void
    {
        $this->account = new Account($this);
    }

    /**
     * Set Client
     * @param ClientInterface $client
     */
    public function setClient(ClientInterface $client)
    {
        $this->httpClient = $client;
    }

    /**
     * Get Client
     * @return ClientInterface
     */
    public function getClient(): ClientInterface
    {
        if (is_null($this->httpClient)) {
            $stack = HandlerStack::create();

            foreach ($this->getConfig()->getMiddleware() as $middlware) {
                $stack->push($middlware);
            }

            $client = new HttpClient([
                RequestOptions::VERIFY => CaBundle::getBundledCaBundlePath(),
                'handler' => $stack,
                'timeout' => $this->getConfig()->getTimeout(),
            ]);

            $this->setClient($client);
        }

        return $this->httpClient;
    }

    /**
     * Retrieve User Agent
     * @return string
     */
    private function getUserAgent(): string
    {
        $agent = $this->getConfig()->getUserAgent();

        return $agent !== '' ? $agent : self::USER_AGENT;
    }

    /**
     * @param string $httpMethod
     * @param string $apiMethod
     * @param string|null $httpBody
     * @param array $requestHeaders
     * @return ResponseInterface
     * @throws CopernicaApiException
     */
    public function performHttpCall(
        string $httpMethod,
        string $apiMethod,
        ?string $httpBody = null,
        array $requestHeaders = []
    ): ResponseInterface {
        $headers = collect([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'User-Agent' => $this->getUserAgent()
        ])
            ->merge($requestHeaders)
            ->all();

        $request = new Request(
            $httpMethod,
            $this->endpoint($apiMethod),
            $headers,
            $httpBody
        );

        try {
            $response = $this->getClient()->send($request, ['http_errors' => false, 'debug' => false]);


            if (!$response) {
                throw new CopernicaApiException('No API response received.');
            }

            return $response;
        } catch (GuzzleException $e) {
            throw new CopernicaRestApi($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Format endpoint with access token
     *
     * @param string $apiMethod
     * @return string
     */
    private function endpoint(string $apiMethod): string
    {
        $arguments = null;

        if (strpos($apiMethod, "?")) {
            // arguments
            list ($apiMethod, $arguments) = explode("?", $apiMethod);
        }
        // append access token.
        $apiMethod .= "?access_token={$this->access_token}";
        if (!is_null($arguments)) {
            $apiMethod .= "&{$arguments}";
        }

        return "{$this->getConfig()->getEndpoint()}/{$apiMethod}";
    }
}
