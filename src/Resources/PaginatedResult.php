<?php
namespace Budgetlens\CopernicaRestApi\Resources;

class PaginatedResult extends BaseResource
{
    public int $start;
    public int $limit;
    public $count;
    public $total;
    public $data;
}
