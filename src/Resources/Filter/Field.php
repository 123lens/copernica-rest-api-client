<?php
namespace Budgetlens\CopernicaRestApi\Resources\Filter;

use Budgetlens\CopernicaRestApi\Resources\BaseResource;

class Field extends BaseResource
{
    public $field;
    public $value;
    public $operator;

    public function __toString()
    {
        return "{$this->field}{$this->operator}{$this->value}";
    }
}