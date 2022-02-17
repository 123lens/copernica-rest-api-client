<?php
namespace Budgetlens\CopernicaRestApi\Endpoints;

use Budgetlens\CopernicaRestApi\Resources\Database as DatabaseResource;
use Budgetlens\CopernicaRestApi\Resources\PaginatedResult;
use Illuminate\Support\Collection;

class Database extends BaseEndpoint
{
    public function list(int $start = 0, int $limit = 1000)
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
}
