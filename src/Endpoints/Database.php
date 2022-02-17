<?php
namespace Budgetlens\CopernicaRestApi\Endpoints;

use Budgetlens\CopernicaRestApi\Resources\Database as DatabaseResource;
use Budgetlens\CopernicaRestApi\Resources\PaginatedResult;
use Budgetlens\CopernicaRestApi\Support\Str;
use Illuminate\Support\Collection;

class Database extends BaseEndpoint
{
    /**
     * List available databases
     * @param int $start
     * @param int $limit
     * @return PaginatedResult
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\RateLimitException
     */
    public function list(int $start = 0, int $limit = 1000): PaginatedResult
    {
        $parameters = collect([
            'start' => $start,
            'limit' => $limit
        ])->reject(function ($value) {
            return empty($value);
        });

        $response = $this->performApiCall(
            'GET',
            "databases" . $this->buildQueryString($parameters->all())
        );

        $items = $response->data ?? null;

        $collection = new Collection();

        if (!is_null($items)) {
            collect($items)->each(function ($item) use ($collection) {
                $collection->push(new DatabaseResource($item));
            });
        }

        return new PaginatedResult([
            'start' => $response->start ?? 0,
            'limit' => $response->limit ?? 0,
            'count' => $response->count ?? 0,
            'data' => $collection
        ]);
    }

    /**
     * Get Database Details
     * @param int $id
     * @return DatabaseResource
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\RateLimitException
     */
    public function get(int $id): DatabaseResource
    {
        $response = $this->performApiCall(
            'GET',
            "database/{$id}"
        );

        return new DatabaseResource(collect($response));
    }

    /**
     * Create new database
     * @param string $name
     * @param string $description
     * @param bool $archived
     * @return DatabaseResource
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\RateLimitException
     */
    public function create(string $name, string $description, bool $archived = false): DatabaseResource
    {
        $data = collect([
            'name' => Str::slug($name, '_'),
            'description' => $description,
            'archived' => $archived
        ])->reject(function ($value) {
            return empty($value);
        });

        $response = $this->performApiCall(
            'POST',
            "databases",
            $data->toJson()
        );

        return new DatabaseResource(array_merge([
            'ID' => $response
        ], $data->toArray()));
    }
}
