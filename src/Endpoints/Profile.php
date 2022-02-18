<?php
namespace Budgetlens\CopernicaRestApi\Endpoints;

use Budgetlens\CopernicaRestApi\Support\FieldFilter;

class Profile extends BaseEndpoint
{
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

    public function updateOrCreate(
        int $databaseId,
        int $id,
        array $fields,
        array $interests = []
    ): int {
        return $this->update($databaseId, $id, $fields, $interests, true);
    }

}
