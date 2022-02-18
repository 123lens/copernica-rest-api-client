<?php
namespace Budgetlens\CopernicaRestApi\Support;

use Budgetlens\CopernicaRestApi\Exceptions\FilterUnknownOperatorException;
use Budgetlens\CopernicaRestApi\Resources\Filter\Field;
use Illuminate\Support\Collection;

class FieldFilter
{
    private $fields;
    private $operators = ['==', '!=', '<>', '<', '>', '<=', '>=', '=~', '!~'];

    public function __construct()
    {
        $this->fields = new Collection();
    }

    /**
     * Output array with each filter as string
     * @return array
     */
    public function toArray()
    {
        $output = [];
        foreach ($this->fields as $field) {
            $output[] = (string)$field;
        }

        return $output;
    }

    /**
     * Add filter
     * @param string $field
     * @param mixed $value
     * @param string $operator
     * @return $this
     * @throws FilterUnknownOperatorException
     */
    public function add(string $field, mixed $value, string $operator = '=='): self
    {
        if (!in_array($operator, $this->operators)) {
            throw new FilterUnknownOperatorException($operator);
        }

        $this->fields->push(new Field([
            'field' => $field,
            'value' => $value,
            'operator' => $operator
        ]));

        return $this;
    }

    public function is(string $field, mixed $value): self
    {
        return $this->add($field, $value, '==');
    }

    public function isNot(string $field, mixed $value): self
    {
        return $this->add($field, $value, '!=');
    }

    public function isGreater(string $field, mixed $value): self
    {
        return $this->add($field, $value, ">");
    }

    public function isGreaterOrEqual(string $field, mixed $value): self
    {
        return $this->add($field, $value, ">=");
    }

    public function isLess(string $field, mixed $value): self
    {
        return $this->add($field, $value, '<');
    }

    public function isLessOrEqual(string $field, mixed $value): self
    {
        return $this->add($field, $value, '<=');
    }

    public function like(string $field, mixed $value, string $match = 'both'): self
    {
        $value = match ($match) {
            'before' => "%{$value}",
            'after' => "{$value}%",
            default => "%{$value}%",
        };

        return  $this->add($field, $value, '=~');
    }

    public function notLike(string $field, mixed $value, string $match = 'both'): self
    {
        $value = match ($match) {
            'before' => "%{$value}",
            'after' => "{$value}%",
            default => "%{$value}%",
        };

        return $this->add($field, $value, '!~');
    }

}