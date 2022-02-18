<?php
namespace Budgetlens\CopernicaRestApi\Resources\Database;

use Budgetlens\CopernicaRestApi\Resources\BaseResource;

class UnsubscribeBehaviour extends BaseResource
{
    public $behavior;
    public $fields;

    public function setFieldsAttribute($value): self
    {
        $this->fields = collect($value);

        return $this;
    }
}
