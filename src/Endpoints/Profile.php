<?php
namespace Budgetlens\CopernicaRestApi\Endpoints;

use Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException;
use Budgetlens\CopernicaRestApi\Exceptions\FilterUnknownOperatorException;
use Budgetlens\CopernicaRestApi\Exceptions\RateLimitException;
use Budgetlens\CopernicaRestApi\Support\FieldFilter;
use Budgetlens\CopernicaRestApi\Resources\Profile as ProfileResource;

class Profile extends BaseEndpoint
{
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
