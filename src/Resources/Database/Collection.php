<?php
namespace Budgetlens\CopernicaRestApi\Resources\Database;

use Budgetlens\CopernicaRestApi\Resources\BaseResource;
use Budgetlens\CopernicaRestApi\Resources\Concerns\HasFields;
use Budgetlens\CopernicaRestApi\Resources\Concerns\HasIntensions;

class Collection extends BaseResource
{
    use HasFields, HasIntensions;

    public $ID;
    public $name;
    public $database;
}
