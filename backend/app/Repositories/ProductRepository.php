<?php

namespace App\Repositories;

use App\Models\Product;
use Core\Repositories\BaseRepository;

class ProductRepository extends BaseRepository
{
    protected string $model = Product::class;

    /**
     * Summary of paginate
     * @param int $page
     * @param int $limit
     * @param string $sortBy
     * @param string $order
     * @return array{data: mixed, meta: array}
     *
     */
    public function paginate(int $page = 1, int $limit = 10, string $sortBy = 'id', string $order = 'desc'): array
    {

        // Define which fields can be used for sorting to prevent SQL injection
        $allowedSortBy = ['id', 'name', 'price', 'status', 'created_at', 'updated_at'];

        if (!in_array($sortBy, $allowedSortBy)) {
            throw new \InvalidArgumentException("Invalid sortBy field: $sortBy");
        }

        // Define which fields can be used for sorting to prevent SQL injection
        $order = strtoLower($order) === 'asc' ? 'asc' : 'desc';

        $paginator = $this->model::orderBy($sortBy, $order)
            ->paginate($limit, ['*'], 'page', $page);

        return [
            'data' => $paginator->items(),
            'meta' => [
                'pagination' => [
                    'page' => $paginator->currentPage(),
                    'pageSize' => $paginator->perPage(),
                    'pageCount' => $paginator->lastPage(),
                    'total' => $paginator->total(),
                    'sortBy' => $sortBy,
                    'order' => $order
                ],
            ],
        ];
    }
}
