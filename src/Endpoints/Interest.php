<?php
namespace Budgetlens\CopernicaRestApi\Endpoints;

use Budgetlens\CopernicaRestApi\Enum\FieldType;
use Budgetlens\CopernicaRestApi\Resources\Database as DatabaseResource;
use Budgetlens\CopernicaRestApi\Resources\Database\UnsubscribeBehaviour;
use Budgetlens\CopernicaRestApi\Resources\PaginatedResult;
use Budgetlens\CopernicaRestApi\Support\Str;
use Illuminate\Support\Collection;

class Interest extends BaseEndpoint
{
    public function update(int $id, string $name = null, string $group = null): bool
    {
        $data = collect([
            'name' => !is_null($name) ? Str::slug($name) : null,
            'group' => $group
        ])->reject(function ($value) {
            return empty($value);
        });

        return $this->performApiCall(
            'PUT',
            "interest/{$id}",
            $data->toJson()
        );
    }
}
