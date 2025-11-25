<?php

namespace App\Repositories;

use App\Models\Supplier;
use Core\Repositories\BaseRepository;

class SupplierRepository extends BaseRepository
{
    protected string $model = Supplier::class;

    /**
     * Paginate suppliers with filters + sorting
     */
    public function paginate(int $page = 1, int $limit = 10, string $sortBy = 'id', string $order = 'asc', array $filters = [], array $include): array
    {

        // Allowed sort fields to avoid SQL injection
        $allowedSortBy = ['id', 'name', 'status', 'created_at', 'updated_at'];

        if (!in_array($sortBy, $allowedSortBy)) {
            throw new \InvalidArgumentException("Invalid sortBy field: $sortBy");
        }

        // normalize sort order
        $order = strtolower($order) === 'desc' ? 'desc' : 'asc';

        $query = $this->model::query();

        if (!empty($include)) {
            $query->with($include);
        }

        // Search in: name, summary, alias, email, phone
        if (!empty($filters['keyword'])) {
            $keyword = trim($filters['keyword']);

            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('summary', 'like', "%{$keyword}%")
                    ->orWhere('alias', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%");
            });
        }

        // Status filter
        if ($filters['status'] !== null && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        //  Execute pagination
        $paginator = $query
            ->orderBy($sortBy, $order)
            ->paginate($limit, ['*'], 'page', $page);

        return [
            'data' => $paginator->items(),
            'meta' => [
                'pagination' => [
                    'page'      => $paginator->currentPage(),
                    'pageSize'  => $paginator->perPage(),
                    'pageCount' => $paginator->lastPage(),
                    'total'     => $paginator->total(),
                    'sortBy'    => $sortBy,
                    'order'     => $order
                ],
            ],
        ];
    }
    public function find(int $id, array $include = []): mixed
    {
        $query = $this->model::query();
        if (!empty($include)) {
            $query->with($include);
        }
        return $query->find($id);
    }
}
