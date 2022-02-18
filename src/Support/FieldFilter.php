<?php
namespace Budgetlens\CopernicaRestApi\Support;

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

    public function toArray()
    {
        $output = [];
        foreach ($this->fields as $field) {
            $output[] = (string)$field;
        }

        return $output;
    }

    public function add(string $field, mixed $value, string $operator = '=='): self
    {
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