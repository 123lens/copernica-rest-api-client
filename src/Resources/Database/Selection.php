<?php
namespace Budgetlens\CopernicaRestApi\Resources\Database;

use Budgetlens\CopernicaRestApi\Resources\BaseResource;
use Budgetlens\CopernicaRestApi\Resources\Concerns\HasFields;
use Budgetlens\CopernicaRestApi\Resources\Concerns\HasIntentions;

class Selection extends BaseResource
{
    use HasIntentions;

    public int $ID;
    public string $name;
    public string $description;
    public string $parentType;
    public int $parentId;
    public bool $hasChildren;
    public bool $hasReferred;
    public bool $hasRules;
    public int $database;
    public \DateTime $lastBuilt;

    /**
     * @param $value
     * @return $this
     */
    public function setParentIdAttribute($value): self
    {
        $this->parentId = (int) $value;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setHasChildrenAttribute($value): self
    {
        $this->hasChildren = (bool) $value;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setHasReferredAttribute($value): self
    {
        $this->hasReferred = (bool) $value;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setHasRulesAttribute($value): self
    {
        $this->hasRules = (bool) $value;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setDatabaseId($value): self
    {
        $this->database = (int) $value;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     * @throws \Exception
     */
    public function setLastBuiltAttribute($value): self
    {
        $this->lastBuilt = new \DateTime($value);

        return $this;
    }
}
