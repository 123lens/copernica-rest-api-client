<?php
namespace Budgetlens\CopernicaRestApi\Resources\Concerns;

use Budgetlens\CopernicaRestApi\Resources\Database\Intentions;

trait HasIntentions
{
    public $intentions;

    public function setIntentionsAttribute($value): self
    {
        if (!$value instanceof Intentions) {
            $value = new Intentions($value);
        }

        $this->intentions = $value;

        return $this;
    }
}
