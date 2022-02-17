<?php
namespace Budgetlens\CopernicaRestApi\Contracts;

interface Jsonable
{
    public function toJson(int $options = 0): string;
}
