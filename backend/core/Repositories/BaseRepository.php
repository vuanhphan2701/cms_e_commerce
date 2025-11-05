<?php

namespace Core\Repositories;

use Exception;

class BaseRepository implements BaseRepositoryInterface
{

    protected $model;

    public function __construct($model)
    {
        if (!property_exists($this, 'model')) {
            throw new Exception(static::class . ' must define the model property.');
        }
        if (!class_exists($model)) {
            throw new Exception("Model class {$this->model} does not exist.");
        }
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find(int $id)
    {
        return $this->model->find($id);
    }
    public function create(array $data)
    {
        return $this->model->create($data);
    }
    public function update(int $id, array $data)
    {
        $record = $this->model->findorFail($id);
        $record->update($data);
        return $record;
    }

    public function delete(int $id)
    {
        $record = $this->model->findorFail($id);
        $record->delete();
        return $record;
    }
}
