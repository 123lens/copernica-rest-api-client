<?php
namespace Budgetlens\CopernicaRestApi\Resources;

use Budgetlens\BolRetailerApi\Resources\Reduction;
use Budgetlens\CopernicaRestApi\Resources\Database\Interest;
use Illuminate\Support\Collection;

class Profile extends BaseResource
{
    public int $ID;
    public $fields;
    public $interests;
    public int $database;
    public string $secret;
    public $created;
    public $modified;
    public bool $removed;

    public function setFieldsAttribute($value): self
    {
        $this->fields = collect($value);

        return $this;
    }

    public function setInterestsAttribute($value): self
    {
        if (is_countable($value)) {
            $items = new Collection();
            collect($value)->each(function ($item) use ($items) {
                if (!$item instanceof Interest) {
                    $item = new Interest([
                        'name' => $item
                    ]);
                }
                $items->push($item);
            });
            $this->interests = $items;
        }

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

    public function setModifiedAttribute($value): self
    {
        if (!$value instanceof \DateTime) {
            $value = new \DateTime($value);
        }

        $this->modified = $value;

        return $this;
    }
}
