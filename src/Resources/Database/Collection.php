<?php
namespace Budgetlens\CopernicaRestApi\Resources\Database;

use Budgetlens\CopernicaRestApi\Resources\BaseResource;
use Budgetlens\CopernicaRestApi\Resources\Concerns\HasFields;
use Budgetlens\CopernicaRestApi\Resources\Concerns\HasIntentions;

class Collection extends BaseResource
{
    use HasFields, HasIntentions;

    public int $ID;
    public string $name;
    public int $database;

    /**
     * @param $value
     * @return $this
     */
    public function setDatabaseAttribute($value): self
    {
        $this->database = (int) $value;

        return $this;
    }
}
