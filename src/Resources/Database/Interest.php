<?php
namespace Budgetlens\CopernicaRestApi\Resources\Database;

use Budgetlens\CopernicaRestApi\Resources\BaseResource;

class Interest extends BaseResource
{
    public int $ID;
    public string $name;
    public string $group;
}
