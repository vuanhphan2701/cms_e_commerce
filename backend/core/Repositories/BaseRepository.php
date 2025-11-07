<?php

namespace Core\Repositories;

use Exception;

class BaseRepository implements BaseRepositoryInterface
{
    protected string $model;

    /**
     * @throws Exception
     */

    public function __construct()
    {
        if (!property_exists($this, 'model')) {
            throw new Exception(static::class . ' must define the $model property.');
        }

        if (!class_exists($this->model)) {
            throw new Exception("Model class {$this->model} does not exist.");
        }
    }

    public function find(int $id): mixed
    {
        return $this->model::find($id);
    }

    public function all(): array
    {
        return $this->model::all()->toArray();
    }

    public function save(array $attributes): mixed
    {
        return $this->model::create($attributes);
    }

    /**
     * @throws Exception
     */
    public function update(int $id, array $attributes): mixed
    {
        $model = $this->model::find($id);

        if (!$model) {
            throw new \Exception("Record not found for ID {$id}");
        }

        $model->update($attributes);

        return $model;
    }

    public function delete(int $id): void
    {
        $this->model::destroy($id);
    }
}
