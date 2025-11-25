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
    public function paginate(int $page = 1, int $limit = 10, string $sortBy = 'id', string $order = 'desc', array $filters = [], array $include = []): array
    {
        // Allowed sorting fields
        $allowedSortBy = ['id', 'name', 'status', 'created_at', 'updated_at'];

        if (!in_array($sortBy, $allowedSortBy)) {
            throw new \InvalidArgumentException("Invalid sortBy field: $sortBy");
        }

        $order = strtolower($order) === 'asc' ? 'asc' : 'desc';

        // Base query
        $query = $this->model::query()->select('categories.*');

        // fetch related entities
        if (!empty($include)) {
            if (in_array('parent', $include)) {
                $query->leftJoin('categories as parent_categories', 'parent_categories.id', '=', 'categories.parent_id')
                    ->addSelect('parent_categories.id as parent_id')
                    ->addSelect('parent_categories.name as parent_name');
            }

            if (in_array('children', $include)) {
                $query->with('children');
            }

             if (in_array('products', $include)) {
        $query->with('products');
    }
        }

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

    public function find(int $id, array $include = []): mixed
    {
        //  dd($include);
        $query =  $this->model::query();
        if (!empty($include)) {
            $query->with($include);
        }
        return $query->find($id);
    }
}
