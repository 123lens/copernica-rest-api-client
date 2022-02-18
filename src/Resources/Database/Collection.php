<?php
namespace Budgetlens\CopernicaRestApi\Resources\Database;

use Budgetlens\CopernicaRestApi\Resources\BaseResource;
use Budgetlens\CopernicaRestApi\Resources\Concerns\HasFields;
use Budgetlens\CopernicaRestApi\Resources\Concerns\HasIntentions;

class Collection extends BaseResource
{
    use HasFields, HasIntentions;

    public $ID;
    public $name;
    public $database;
}
