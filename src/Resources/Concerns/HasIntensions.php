<?php
namespace Budgetlens\CopernicaRestApi\Resources\Concerns;

use Budgetlens\CopernicaRestApi\Resources\Database\Intensions;

trait HasIntensions
{
    public $intentions;

    public function setIntensionsAttribute($value): self
    {
        if (!$value instanceof Intensions) {
            $value = new Intensions($value);
        }

        $this->intentions = $value;

        return $this;
    }
}