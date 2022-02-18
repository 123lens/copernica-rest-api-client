<?php
namespace Budgetlens\CopernicaRestApi\Endpoints;

use Budgetlens\CopernicaRestApi\Support\FieldFilter;

class Profile extends BaseEndpoint
{
    public function updateProfile(
        int $databaseId,
        int $id,
        array $fields,
        array $interests = [],
        bool $create = false
    ):bool {
        // set filter for single profile
        $filter = new FieldFilter();
        $filter->add('ID', $id);

        $parameters = collect([
            'async' => false,
            'create' => $create,
            'fields[]' => $filter->toArray()
        ]);

        // collect data.
        $data = collect([
            'fields' => $fields,
            'interests' => $interests
        ])->reject(function ($value) {
            return !count($value);
        });

        return $this->performApiCall(
            'PUT',
            "database/{$databaseId}/profiles" . $this->buildQueryString($parameters->all()),
            $data->toJson()
        );

    }
}
