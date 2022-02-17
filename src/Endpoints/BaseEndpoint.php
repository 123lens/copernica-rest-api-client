<?php
namespace Budgetlens\CopernicaRestApi\Endpoints;

use Budgetlens\CopernicaRestApi\Client;

use Budgetlens\CopernicaRestApi\Support\Uri;
use Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException;
use Budgetlens\CopernicaRestApi\Exceptions\RateLimitException;

abstract class BaseEndpoint
{
    /** @var \Budgetlens\CopernicaRestApi\Client */
    protected $apiClient;

    public function __construct(Client $client)
    {
        $this->apiClient = $client;

        $this->boot();
    }

    protected function boot(): void
    {
    }

    /**
     * Build query string
     * @param array $filters
     * @return string
     */
    protected function buildQueryString(array $filters): string
    {
        if (empty($filters)) {
            return '';
        }

        $query = Uri::queryString($filters);

        return "?{$query}";
    }

    /**
     * Performs a HTTP call to the API endpoint.
     *
     * @param  string  $httpMethod
     * @param  string  $apiMethod
     * @param  string|null  $httpBody
     * @param  array  $requestHeaders
     * @return string|object|null
     *
     */
    protected function performApiCall(
        string $httpMethod,
        string $apiMethod,
        ?string $httpBody = null,
        array $requestHeaders = []
    ) {
        $response = $this->apiClient->performHttpCall($httpMethod, $apiMethod, $httpBody, $requestHeaders);

        // hit a rate limit ?
        if ($response->getStatusCode() === 429) {
            $retryAfter = collect($response->getHeader('Retry-After'))->first();
            throw new RateLimitException($retryAfter);
        }
        // error handling
        if ($response->getStatusCode() >= 400) {
            throw new CopernicaApiException($response->getBody());
        }

        $directResponseHeaders = [
        ];

        if (in_array(collect($response->getHeader('Content-Type'))->first(), $directResponseHeaders)) {
            return $response->getBody()->getContents();
        }

        $body = $response->getBody()->getContents();

        if (empty($body)) {
            if ($response->getStatusCode() === Client::HTTP_STATUS_NO_CONTENT) {
                return ;
            }

            throw new CopernicaApiException('No response body found.');
        }

        $object = @json_decode($body);

        if (json_last_error() != JSON_ERROR_NONE) {
            throw new CopernicaApiException("Unable to decode response: '{$body}'.");
        }

        return $object;
    }
}
