<?php
namespace Budgetlens\CopernicaRestApi\Endpoints;

use Budgetlens\CopernicaRestApi\Client;

use Budgetlens\CopernicaRestApi\Support\Uri;
use Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException;
use Budgetlens\CopernicaRestApi\Exceptions\RateLimitException;
use Illuminate\Support\Collection;

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
     * Paginate filter
     * @param int $start
     * @param int $limit
     * @param bool $calculateTotal
     * @return Collection
     */
    public function paginateFilter(int $start = 0, int $limit = 1000, bool $calculateTotal = false): Collection
    {
        return collect([
            'start' => $start,
            'limit' => $limit,
            'total' => $calculateTotal
        ])->reject(function ($value) {
            return empty($value);
        });
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

        // recevied an "X-Created" header?
        if ($response->hasHeader('X-Created')) {
            // return created ID
            return collect($response->getHeader('X-Created'))->first();
        }

        // PUT response.
        if ((strtoupper($httpMethod === 'PUT') || strtoupper($httpMethod) === 'DELETE')
            && $response->getStatusCode() === 204
        ) {
            return true;
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

        // error handling
        if ($response->getStatusCode() >= 400) {
            $messageBag = collect('Error executing API call');

            $error = collect($object->error ?? []);

            if ($error->has('message')) {
                $messageBag->push(': ' . $error->get('message'));
            }

            throw new CopernicaApiException($messageBag->implode(' '), $response->getStatusCode());
        }


        return $object;
    }
}
