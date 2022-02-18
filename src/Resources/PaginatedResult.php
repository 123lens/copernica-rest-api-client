<?php
namespace Budgetlens\CopernicaRestApi\Resources;

class PaginatedResult extends BaseResource
{
    public int $start;
    public int $limit;
    public $count;
    public $total;
    public $data;

    public function setStartAttribute($value): self
    {
        $this->start = (int) $value;

        return $this;
    }

    public function setCountAttribute($value): self
    {
        $this->count = (int) $value;

        return $this;
    }

    public function setTotalAttribute($value): self
    {
        $this->total = (int) $value;

        return $this;
    }
}
