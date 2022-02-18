<?php
namespace Budgetlens\CopernicaRestApi\Resources\Database;

use Budgetlens\CopernicaRestApi\Resources\BaseResource;

class Field extends BaseResource
{
    public int $ID;
    public string $name;
    public string $type;
    public mixed $value;
    public bool $displayed;
    public bool $ordered;
    public int $length;
    public int $textlines;
    public bool $hidden;
    public bool $index;
}
