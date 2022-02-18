<?php
namespace Budgetlens\CopernicaRestApi\Endpoints;

use Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException;
use Budgetlens\CopernicaRestApi\Exceptions\FilterUnknownOperatorException;
use Budgetlens\CopernicaRestApi\Exceptions\RateLimitException;
use Budgetlens\CopernicaRestApi\Resources\PaginatedResult;
use Budgetlens\CopernicaRestApi\Support\FieldFilter;
use Budgetlens\CopernicaRestApi\Resources\Profile as ProfileResource;
use Illuminate\Support\Collection;

class Profile extends BaseEndpoint
{
    /**
     * Get Profiles
     * @todo: Implement orderBy / orderDir / DataOnly
     * @param int $id
     * @param int $start
     * @param int $limit
     * @param bool $calculateTotal
     * @param FieldFilter|null $fields
     * @param string|null $orderBy
     * @param string|null $orderDirection
     * @param bool|null $dataOnly
     * @return PaginatedResult
     * @throws CopernicaApiException
     * @throws RateLimitException
     */
    public function list(
        int $id,
        int $start = 0,
        int $limit = 1000,
        bool $calculateTotal = false,
        FieldFilter $fields = null,
        string $orderBy = null,
        string $orderDirection = null,
        bool $dataOnly = null
    ): PaginatedResult {
        $pagination = $this->paginateFilter($start, $limit, $calculateTotal);

        $parameters = collect(array_merge($pagination->all(), [
            'fields[]' => !is_null($fields) ? $fields->toArray() : null
        ]));

        $response = $this->performApiCall(
            'GET',
            "database/{$id}/profiles" . $this->buildQueryString($parameters->all())
        );

        $items = $response->data ?? null;

        $collection = new Collection();

        if (!is_null($items)) {
            collect($items)->each(function ($item) use ($collection) {
                $collection->push(new ProfileResource($item));
            });
        }

        return new PaginatedResult([
            'start' => $response->start ?? 0,
            'limit' => $response->limit ?? 0,
            'total' => $calculateTotal ? ($response->total ?? 0) : null,
            'count' => $calculateTotal ? ($response->count ?? 0) : null,
            'data' => $collection
        ]);
    }


    /**
     * Create Database Profile
     * @param int $id
     * @param array $fields
     * @param array $interests
     * @return ProfileResource
     * @throws CopernicaApiException
     * @throws RateLimitException
     */
    public function create(int $id, array $fields, array $interests = []): ProfileResource
    {
        $data = collect([
            'fields' => $fields,
            'interests' => $interests
        ])->reject(function ($value) {
            return !count($value);
        });

        $response = $this->performApiCall(
            'POST',
            "database/{$id}/profiles",
            $data->toJson()
        );

        return new ProfileResource(array_merge([
            'ID' => $response
        ], $data->toArray()));
    }


    /**
     * Update propfile.
     *
     * @todo: Api always returns http status 204.. (in case of create optional 201)
     *          Not sure how to fetch failures.
     * @param int $databaseId
     * @param int $id
     * @param array $fields
     * @param array $interests
     * @param bool $create
     * @return int
     * @throws CopernicaApiException
     * @throws FilterUnknownOperatorException
     * @throws RateLimitException
     */
    public function update(
        int $databaseId,
        int $id,
        array $fields,
        array $interests = [],
        bool $create = false
    ): int {
        // set filter for single profile
        $filter = new FieldFilter();
        $filter->add('ID', $id);

        $parameters = collect([
            'async' => false,
            'create' => $create,
            'fields[]' => $filter->toArray()
        ])->reject(function ($value) {
            return empty($value);
        });

        // collect data.
        $data = collect([
            'fields' => $fields,
            'interests' => $interests
        ])->reject(function ($value) {
            return !count($value);
        });

        $response = $this->performApiCall(
            'PUT',
            "database/{$databaseId}/profiles" . $this->buildQueryString($parameters->all()),
            $data->toJson()
        );
        if (is_numeric($response)) {
            // create new record.
            return $response;
        }

        return is_bool($response) && $response === true
            ? $id
            : 0;
    }

    /**
     * Update or create profile
     * @param int $databaseId
     * @param int $id
     * @param array $fields
     * @param array $interests
     * @return int
     * @throws CopernicaApiException
     * @throws FilterUnknownOperatorException
     * @throws RateLimitException
     */
    public function updateOrCreate(
        int $databaseId,
        int $id,
        array $fields,
        array $interests = []
    ): int {
        return $this->update($databaseId, $id, $fields, $interests, true);
    }

    /**
     * Delete profiles from database
     * @param int $databaseId
     * @param array $ids
     * @return bool|int|object|string|null
     * @throws CopernicaApiException
     * @throws RateLimitException
     */
    public function delete(int $databaseId, array $ids = [])
    {
        $data = collect([
            'profiles' => $ids,
        ])->reject(function ($value) {
            return !count($value);
        });

        return $this->performApiCall(
            'DELETE',
            "database/{$databaseId}/profiles",
            $data->toJson()
        );
    }
}
