<?php
namespace Budgetlens\CopernicaRestApi\Resources;

use Budgetlens\BolRetailerApi\Resources\Reduction;
use Budgetlens\CopernicaRestApi\Resources\Concerns\HasFields;
use Budgetlens\CopernicaRestApi\Resources\Concerns\HasIntensions;
use Budgetlens\CopernicaRestApi\Resources\Database\Interest;
use Illuminate\Support\Collection;

class Database extends BaseResource
{
    use HasFields, HasIntensions;

    public int $ID;
    public string $name;
    public string $description;
    public bool $archived;
    public \DateTime $created;
    public $interests;
    public $collections;

    public function setArchivedAttribute($value): self
    {
        $this->archived = (bool) $value;

        return $this;
    }

    public function setCreatedAttribute($value): self
    {
        if (!$value instanceof \DateTime) {
            $value = new \DateTime($value);
        }

        $this->created = $value;

        return $this;
    }

    public function setInterestsAttribute($value): self
    {
        $data = $value->data ?? [];

        if (count($data)) {
            $items = new Collection();
            collect($data)->each(function ($item) use ($items) {
                if (!$item instanceof Interest) {
                    $item = new Interest($item);
                }
                $items->push($item);
            });

            $this->interests = new PaginatedResult([
                'start' => $value->start ?? 0,
                'limit' => $value->limit ?? 0,
                'count' => $value->count ?? 0,
                'data' => $items
            ]);
        }

        return $this;
    }

    public function setCollectionsAttribute($value): self
    {
        $data = $value->data ?? [];

        if (count($data)) {
            $items = new Collection();
            collect($data)->each(function ($item) use ($items) {
                if (!$item instanceof Collection) {
                    $item = new Collection($item);
                }
                $items->push($item);
            });

            $this->collections = new PaginatedResult([
                'start' => $value->start ?? 0,
                'limit' => $value->limit ?? 0,
                'count' => $value->count ?? 0,
                'data' => $items
            ]);
        }

        return $this;
    }
}
