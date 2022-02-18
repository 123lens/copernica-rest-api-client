<?php
namespace Budgetlens\CopernicaRestApi\Resources\Concerns;

use Budgetlens\CopernicaRestApi\Resources\Database\Field;
use Budgetlens\CopernicaRestApi\Resources\PaginatedResult;
use Illuminate\Support\Collection;

trait HasFields
{
    public $fields;

    public function setFieldsAttribute($value): self
    {
        $data = $value->data ?? [];

        if (count($data)) {
            $items = new Collection();
            collect($data)->each(function ($item) use ($items) {
                if (!$item instanceof Field) {
                    $item = new Field($item);
                }
                $items->push($item);
            });

            $this->fields = new PaginatedResult([
                'start' => $value->start ?? 0,
                'limit' => $value->limit ?? 0,
                'count' => $value->count ?? 0,
                'data' => $items
            ]);
        }

        return $this;
    }
}
