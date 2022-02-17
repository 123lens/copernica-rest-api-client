<?php
namespace Budgetlens\CopernicaRestApi\Resources\Database;

use Budgetlens\CopernicaRestApi\Resources\BaseResource;

class Field extends BaseResource
{
    public $ID;
    public $name;
    public $type;
    public $value;
    public $displayed;
    public $ordered;
    public $length;
    public $textlines;
    public $hidden;
    public $index;
}
