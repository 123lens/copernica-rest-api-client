<?php
namespace Budgetlens\CopernicaRestApi\Endpoints;

use Budgetlens\CopernicaRestApi\Enum\FieldType;
use Budgetlens\CopernicaRestApi\Resources\Database as DatabaseResource;
use Budgetlens\CopernicaRestApi\Resources\Database\UnsubscribeBehaviour;
use Budgetlens\CopernicaRestApi\Resources\PaginatedResult;
use Budgetlens\CopernicaRestApi\Resources\Profile;
use Budgetlens\CopernicaRestApi\Support\FieldFilter;
use Budgetlens\CopernicaRestApi\Support\Str;
use Illuminate\Support\Collection;

class Database extends BaseEndpoint
{
    /**
     * List available databases
     * @param int $start
     * @param int $limit
     * @param bool $calculateTotal
     * @return PaginatedResult
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\RateLimitException
     */
    public function list(int $start = 0, int $limit = 1000, bool $calculateTotal  = false): PaginatedResult
    {
        $parameters = $this->paginateFilter($start, $limit, $calculateTotal);

        $response = $this->performApiCall(
            'GET',
            "databases" . $this->buildQueryString($parameters->all())
        );

        $items = $response->data ?? null;

        $collection = new Collection();

        if (!is_null($items)) {
            collect($items)->each(function ($item) use ($collection) {
                $collection->push(new DatabaseResource($item));
            });
        }

        return new PaginatedResult([
            'start' => $response->start ?? 0,
            'limit' => $response->limit ?? 0,
            'count' => $calculateTotal ? ($response->count ?? 0) : null,
            'data' => $collection
        ]);
    }

    /**
     * Get Database Details
     * @param int $id
     * @return DatabaseResource
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\RateLimitException
     */
    public function get(int $id): DatabaseResource
    {
        $response = $this->performApiCall(
            'GET',
            "database/{$id}"
        );

        return new DatabaseResource(collect($response));
    }

    /**
     * Create new database
     * @param string $name
     * @param string $description
     * @param bool $archived
     * @return DatabaseResource
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\RateLimitException
     */
    public function create(string $name, string $description, bool $archived = false): DatabaseResource
    {
        $data = collect([
            'name' => Str::slug($name),
            'description' => $description,
            'archived' => $archived
        ])->reject(function ($value) {
            return empty($value);
        });

        $response = $this->performApiCall(
            'POST',
            "databases",
            $data->toJson()
        );

        return new DatabaseResource(array_merge([
            'ID' => $response
        ], $data->toArray()));
    }

    /**
     * Copy database
     * @param int $id
     * @param string $name
     * @param array $options
     * @return DatabaseResource
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\RateLimitException
     */
    public function copy(int $id, string $name, array $options = [])
    {
        // stel de opties voor de kopie in
        $options = array_merge([
            'collections'   =>  true,
            'miniviews'     =>  true,
            'views'         =>  true,
            'profiles'      =>  true,
            'subprofiles'   =>  true
        ], $options);

        $data = collect([
            'name' => Str::slug($name),
            'options' => $options
        ])->reject(function ($value) {
            return empty($value);
        });

        $response = $this->performApiCall(
            'POST',
            "database/{$id}/copy",
            $data->toJson()
        );

        return new DatabaseResource(array_merge([
            'ID' => $response
        ], $data->toArray()));
    }

    /**
     * Update database information
     * @param int $id
     * @param string|null $name
     * @param string|null $description
     * @param bool|null $archived
     * @param \DateTime|null $created
     * @param array $fields
     * @param array $interests
     * @param array $collections
     * @return bool
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\RateLimitException
     */
    public function update(
        int $id,
        string $name = null,
        string $description = null,
        bool $archived = null,
        \DateTime $created = null,
        array $fields = [],
        array $interests   = [],
        array $collections = []
    ): bool {
        $data = collect([
            'name' => Str::slug($name),
            'description' => $description,
            'archived' => $archived,
            'created' => $created,
            'fields' => $fields,
            'interests' => $interests,
            'collections' => $collections
        ])->reject(function ($value) {
            return empty($value) ||
                (is_array($value) && !count($value));
        });

        return $this->performApiCall(
            'PUT',
            "database/{$id}",
            $data->toJson()
        );
    }

    /**
     * Get Database Unsubscribe Behaviour
     * @param int $id
     * @return UnsubscribeBehaviour
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\RateLimitException
     */
    public function getUnsubscribeBehaviour(int $id): UnsubscribeBehaviour
    {
        $response = $this->performApiCall(
            'GET',
            "database/{$id}/unsubscribe"
        );

        return new UnsubscribeBehaviour(collect($response));
    }

    /**
     * Update unsubscribe behaviour
     * @param int $id
     * @param string $behaviour
     * @param array $fields
     * @return bool
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\RateLimitException
     */
    public function updateUnsubscribeBehaviour(int $id, string $behaviour, array $fields = []): bool
    {
        $data = collect([
            'behaviour' => $behaviour,
            'fields' => $fields
        ])->reject(function ($value) {
            return empty($value) ||
                (is_array($value) && !count($value));
        });

        return $this->performApiCall(
            'PUT',
            "database/{$id}/unsubscribe",
            $data->toJson()
        );
    }

    /**
     * Get Database Selections
     * @param int $id
     * @param int $start
     * @param int $limit
     * @param bool $calculateTotal
     * @return PaginatedResult
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\RateLimitException
     */
    public function getSelections(
        int $id,
        int $start = 0,
        int $limit = 1000,
        bool $calculateTotal = false
    ): PaginatedResult {
        $parameters = $this->paginateFilter($start, $limit, $calculateTotal);

        $response = $this->performApiCall(
            'GET',
            "database/{$id}/views" . $this->buildQueryString($parameters->all())
        );

        $items = $response->data ?? null;

        $collection = new Collection();

        if (!is_null($items)) {
            collect($items)->each(function ($item) use ($collection) {
                $collection->push(new DatabaseResource\Selection($item));
            });
        }

        return new PaginatedResult([
            'start' => $response->start ?? 0,
            'limit' => $response->limit ?? 0,
            'count' => $calculateTotal ? ($response->count ?? 0) : null,
            'data' => $collection
        ]);
    }

    /**
     * Create Database Selection
     *
     * @todo: "description" is being ignored bug?
     * @param int $id
     * @param string $name
     * @param string $description
     * @return DatabaseResource\Selection
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\RateLimitException
     */
    public function createSelection(int $id, string $name, string $description): DatabaseResource\Selection {
        $data = collect([
            'name' => Str::slug($name),
            'description' => $description,
        ])->reject(function ($value) {
            return empty($value);
        });

        $response = $this->performApiCall(
            'POST',
            "database/{$id}/views",
            $data->toJson()
        );

        return new DatabaseResource\Selection(array_merge([
            'ID' => $response
        ], $data->toArray()));
    }

    /**
     * Get Database Collections
     * @param int $id
     * @param int $start
     * @param int $limit
     * @param bool $calculateTotal
     * @return PaginatedResult
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\RateLimitException
     */
    public function getCollections(
        int $id,
        int $start = 0,
        int $limit = 1000,
        bool $calculateTotal = false
    ): PaginatedResult {
        $parameters = $this->paginateFilter($start, $limit, $calculateTotal);

        $response = $this->performApiCall(
            'GET',
            "database/{$id}/collections" . $this->buildQueryString($parameters->all())
        );

        $items = $response->data ?? null;

        $collection = new Collection();

        if (!is_null($items)) {
            collect($items)->each(function ($item) use ($collection) {
                $collection->push(new DatabaseResource\Collection($item));
            });
        }

        return new PaginatedResult([
            'start' => $response->start ?? 0,
            'limit' => $response->limit ?? 0,
            'count' => $calculateTotal ? ($response->count ?? 0) : null,
            'data' => $collection
        ]);
    }

    /**
     * Create Database Collection
     * @param int $id
     * @param string $name
     * @return Collection
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\RateLimitException
     */
    public function createCollection(int $id, string $name): DatabaseResource\Collection
    {
        $data = collect([
            'name' => Str::slug($name),
        ])->reject(function ($value) {
            return empty($value);
        });

        $response = $this->performApiCall(
            'POST',
            "database/{$id}/collections",
            $data->toJson()
        );

        return new DatabaseResource\Collection(array_merge([
            'ID' => $response
        ], $data->toArray()));
    }

    /**
     * Get Database Fields
     * @param int $id
     * @param int $start
     * @param int $limit
     * @param bool $calculateTotal
     * @return PaginatedResult
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\RateLimitException
     */
    public function getFields(
        int $id,
        int $start = 0,
        int $limit = 1000,
        bool $calculateTotal = false
    ): PaginatedResult {
        $parameters = $this->paginateFilter($start, $limit, $calculateTotal);

        $response = $this->performApiCall(
            'GET',
            "database/{$id}/fields" . $this->buildQueryString($parameters->all())
        );

        $items = $response->data ?? null;

        $collection = new Collection();

        if (!is_null($items)) {
            collect($items)->each(function ($item) use ($collection) {
                $collection->push(new DatabaseResource\Field($item));
            });
        }

        return new PaginatedResult([
            'start' => $response->start ?? 0,
            'limit' => $response->limit ?? 0,
            'count' => $calculateTotal ? ($response->count ?? 0) : null,
            'data' => $collection
        ]);
    }

    public function createField(
        int $id,
        string $name,
        FieldType $type,
        mixed $value = null,
        bool $displayed = false,
        bool $ordered = false,
        int $length = 0,
        int $textlines = 0,
        bool $hidden = false,
        bool $index = false
    ): DatabaseResource\Field {
        $data = collect([
            'name' => Str::slug($name),
            'type' => $type->value,
            'value' => $type->prepValue($value),
            'displayed' => $displayed,
            'ordered' => $ordered,
            'length' => $length,
            'textlines' => $textlines,
            'hidden' => $hidden,
            'index' => $index
        ])->reject(function ($value) {
            return empty($value);
        });

        $response = $this->performApiCall(
            'POST',
            "database/{$id}/fields",
            $data->toJson()
        );

        return new DatabaseResource\Field(array_merge([
            'ID' => $response
        ], $data->toArray()));
    }

    /**
     * @param int $databaseId
     * @param int $id
     * @param string $name
     * @param FieldType|null $type
     * @param mixed|null $value
     * @param bool $displayed
     * @param bool $ordered
     * @param int $length
     * @param int $textlines
     * @param bool $hidden
     * @param bool $index
     * @return bool
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\RateLimitException
     */
    public function updateField(
        int $databaseId,
        int $id,
        string $name,
        FieldType $type = null,
        mixed $value = null,
        bool $displayed = false,
        bool $ordered = false,
        int $length = 0,
        int $textlines = 0,
        bool $hidden = false,
        bool $index = false
    ): bool {
        $data = collect([
            'name' => Str::slug($name),
            'type' => $type->value ?? null,
            'value' => !is_null($type) ?$type->prepValue($value) : $value,
            'displayed' => $displayed,
            'ordered' => $ordered,
            'length' => $length,
            'textlines' => $textlines,
            'hidden' => $hidden,
            'index' => $index
        ])->reject(function ($value) {
            return empty($value) ||
                (is_array($value) && !count($value));
        });

        return $this->performApiCall(
            'PUT',
            "database/{$databaseId}/field/{$id}",
            $data->toJson()
        );
    }

    /**
     * Delete Database Field
     * @param int $databaseId
     * @param int $id
     * @return bool
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\RateLimitException
     */
    public function deleteField(int $databaseId, int $id): bool
    {
        return $this->performApiCall(
            'DELETE',
            "database/{$databaseId}/field/{$id}"
        );
    }

    /**
     * Get Database Interests
     * @param int $id
     * @param int $start
     * @param int $limit
     * @param bool $calculateTotal
     * @return PaginatedResult
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\RateLimitException
     */
    public function getInterests(
        int $id,
        int $start = 0,
        int $limit = 1000,
        bool $calculateTotal  = false
    ): PaginatedResult {
        $parameters = $this->paginateFilter($start, $limit, $calculateTotal);

        $response = $this->performApiCall(
            'GET',
            "database/{$id}/interests" . $this->buildQueryString($parameters->all())
        );

        $items = $response->data ?? null;

        $collection = new Collection();

        if (!is_null($items)) {
            collect($items)->each(function ($item) use ($collection) {
                $collection->push(new DatabaseResource\Interest($item));
            });
        }

        return new PaginatedResult([
            'start' => $response->start ?? 0,
            'limit' => $response->limit ?? 0,
            'count' => $calculateTotal ? ($response->count ?? 0) : null,
            'data' => $collection
        ]);
    }

    /**
     * Create Database Interest
     * @param int $id
     * @param string $name
     * @param string|null $group
     * @return DatabaseResource\Interest
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\RateLimitException
     */
    public function createInterest(int $id, string $name, string $group = null): DatabaseResource\Interest
    {
        $data = collect([
            'name' => Str::slug($name),
            'group' => $group
        ])->reject(function ($value) {
            return empty($value);
        });

        $response = $this->performApiCall(
            'POST',
            "database/{$id}/interests",
            $data->toJson()
        );

        return new DatabaseResource\Interest(array_merge([
            'ID' => $response
        ], $data->toArray()));
    }

    /**
     * Get Database Profile Ids
     * @param int $id
     * @return Collection
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\RateLimitException
     */
    public function getProfileIds(int $id): Collection
    {
        $response = $this->performApiCall(
            'GET',
            "database/{$id}/profileids?limit=100q",
        );

        return collect($response);
    }

    /**
     * Get Profiles
     * @param int $id
     * @param int $start
     * @param int $limit
     * @param bool $calculateTotal
     * @param string|null $fields
     * @param string|null $orderBy
     * @param string|null $orderDirection
     * @param bool|null $dataOnly
     * @return PaginatedResult
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\RateLimitException
     */
    public function getProfiles(
        int $id,
        int $start = 0,
        int $limit = 1000,
        bool $calculateTotal  = false,
        FieldFilter $fields = null,
        string $orderBy = null,
        string $orderDirection = null,
        bool $dataOnly = null
    ): PaginatedResult {
        $pagination = $this->paginateFilter($start, $limit, $calculateTotal);

        $parameters = collect(array_merge($pagination->all(), [
            'fields[]' => !is_null($fields) ? $fields->toArray() : null
        ]));

        $response = $this->performApiCall(
            'GET',
            "database/{$id}/profiles" . $this->buildQueryString($parameters->all())
        );

        $items = $response->data ?? null;

        $collection = new Collection();

        if (!is_null($items)) {
            collect($items)->each(function ($item) use ($collection) {
                $collection->push(new Profile($item));
            });
        }

        return new PaginatedResult([
            'start' => $response->start ?? 0,
            'limit' => $response->limit ?? 0,
            'total' => $calculateTotal ? ($response->total ?? 0) : null,
            'count' => $calculateTotal ? ($response->count ?? 0) : null,
            'data' => $collection
        ]);
    }

    /**
     * Create Database Profile
     * @param int $id
     * @param array $fields
     * @param array $interests
     * @return Profile
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\CopernicaApiException
     * @throws \Budgetlens\CopernicaRestApi\Exceptions\RateLimitException
     */
    public function createProfile(int $id, array $fields, array $interests = []): Profile
    {
        $data = collect([
            'fields' => $fields,
            'interests' => $interests
        ])->reject(function ($value) {
            return !count($value);
        });

        $response = $this->performApiCall(
            'POST',
            "database/{$id}/profiles",
            $data->toJson()
        );

        return new Profile(array_merge([
            'ID' => $response
        ], $data->toArray()));
    }
}
