<?php
namespace Budgetlens\CopernicaRestApi\Resources;

class PaginatedResult extends BaseResource
{
    public $start;
    public $limit;
    public $count;
    public $data;
}
