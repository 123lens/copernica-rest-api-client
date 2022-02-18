<?php
namespace Budgetlens\CopernicaRestApi\Resources\Filter;

use Budgetlens\CopernicaRestApi\Resources\BaseResource;

class Field extends BaseResource
{
    public string $field;
    public mixed $value;
    public string $operator;

    public function __toString()
    {
        return "{$this->field}{$this->operator}{$this->value}";
    }
}
