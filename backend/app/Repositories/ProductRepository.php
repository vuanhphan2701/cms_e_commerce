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
     * @param array $filters
     * @return array{data: mixed, meta: array}
     */
    public function paginate(
        int $page = 1,
        int $limit = 10,
        string $sortBy = 'id',
        string $order = 'desc',
        array $filters = [],
        array $include = []
    ): array {
        // list allowed sorting fields
        $allowedSortBy = ['id', 'name', 'price', 'status', 'created_at', 'updated_at'];

        if (!in_array($sortBy, $allowedSortBy)) {
            throw new \InvalidArgumentException("Invalid sortBy field: $sortBy");
        }

        // Normalize sorting direction
        $order = strtolower($order) === 'asc' ? 'asc' : 'desc';

        // Build base query
        $query = $this->model::query();

        if (!empty($include)) {
            $query->with($include);
        }
        // Keyword search (name, sku, alias)
        if (!empty($filters['keyword'])) {
            $keyword = trim($filters['keyword']);
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('sku', 'like', "%{$keyword}%")
                    ->orWhere('alias', 'like', "%{$keyword}%");
            });
        }

        // Apply filters for category, brand, supplier, status
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }
        if (!empty($filters['supplier_id'])) {
            $query->where('supplier_id', $filters['supplier_id']);
        }
        if ($filters['status'] !== null && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

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
                ],
            ],
        ];
    }
}
