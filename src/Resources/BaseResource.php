<?php
namespace Budgetlens\CopernicaRestApi\Resources;

use Budgetlens\CopernicaRestApi\Contracts\Arrayable;
use Budgetlens\CopernicaRestApi\Contracts\Jsonable;
use Budgetlens\CopernicaRestApi\Exceptions\JsonEncodingException;
use Budgetlens\CopernicaRestApi\Resources\Concerns\HasAttributes;
use JsonSerializable;

abstract class BaseResource implements Arrayable, Jsonable, JsonSerializable
{
    use HasAttributes;

    /**
     * Force ID Field to be int
     * @param $value
     * @return $this
     */
    public function setIDAttribute($value): self
    {
        $this->ID = (int) $value;

        return $this;
    }

    /**
     * BaseResource constructor.
     * @param array $attributes
     */
    public function __construct($attributes = [])
    {
        $this->fill($attributes);
    }

    /**
     * Fill object
     * @param $attributes
     * @return $this
     */
    public function fill($attributes): self
    {
        collect($attributes)->each(function ($value, $key) {
            $this->setAttribute($key, $value);
        });

        return $this;
    }

    /**
     * Json Serialize
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Output to array
     * @return array
     */
    public function toArray(): array
    {
        return collect($this->attributesToArray())
            ->reject(function ($value) {
                return $value === null;
            })
            ->all();
    }

    /**
     * Output as json
     * @param int $options
     * @return string
     */
    public function toJson(int $options = 0): string
    {
        $json = json_encode($this->jsonSerialize(), $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw JsonEncodingException::forResource($this, json_last_error_msg());
        }

        return $json;
    }

    /**
     * Dynamically retrieve attributes on the resource.
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Dynamically set attributes on the resource.
     * @param string $key
     * @param $value
     */
    public function __set(string $key, $value): void
    {
        $this->setAttribute($key, $value);
    }
}
