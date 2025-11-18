<?php

namespace App\Repositories;

use App\Models\Category;
use Core\Repositories\BaseRepository;

class CategoryRepository extends BaseRepository
{
    protected string $model = Category::class;

    /**
     * Paginate + filter + search
     */
    public function paginate(
        int $page = 1,
        int $limit = 10,
        string $sortBy = 'id',
        string $order = 'desc',
        array $filters = []
    ): array {

        // Allowed sorting fields
        $allowedSortBy = ['id', 'name', 'status', 'created_at', 'updated_at'];

        if (!in_array($sortBy, $allowedSortBy)) {
            throw new \InvalidArgumentException("Invalid sortBy field: $sortBy");
        }

        $order = strtolower($order) === 'asc' ? 'asc' : 'desc';

        // Base query
        $query = $this->model::query();

        // Keyword search in: name, summary, alias
        if (!empty($filters['keyword'])) {
            $keyword = trim($filters['keyword']);

            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('alias', 'like', "%{$keyword}%")
                  ->orWhere('summary', 'like', "%{$keyword}%");
            });
        }

        // Filter by status
        if (isset($filters['status']) && $filters['status'] !== null) {
            $query->where('status', $filters['status']);
        }

        // Parent category filter
        if (!empty($filters['parent_id'])) {
            $query->where('parent_id', $filters['parent_id']);
        }

        // Sorting + pagination
        $paginator = $query
            ->orderBy($sortBy, $order)
            ->paginate($limit, ['*'], 'page', $page);

        // Return uniform structure
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
                ]
            ]
        ];
    }
}
