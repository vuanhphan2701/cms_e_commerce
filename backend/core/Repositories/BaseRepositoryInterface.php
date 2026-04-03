<?php
namespace Core\Repositories;

interface BaseRepositoryInterface
{
    /**
     * Find a record by ID.
     *
     * @param int $id
     * @return mixed
     */
    public function find(int $id): mixed;

    /**
     * Get all records
     *
     * @return array
     */
    public function all(): array;

    /**
     * Save a record (update or insert)
     *
     * @param array $attributes
     * @return mixed
     */
    public function save(array $attributes): mixed;

    /**
     * Update an existing record by ID.
     *
     * @param int $id
     * @param array $attributes
     * @return mixed
     */
    public function update(int $id, array $attributes): mixed;

    /**
     * Delete a record by ID
     *
     * @param int $id
     * @return void
     */
    public function delete(int $id): void;
}
