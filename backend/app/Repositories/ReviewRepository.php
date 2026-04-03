<?php

namespace App\Repositories;

use App\Models\Review;
use Core\Repositories\BaseRepository;

class ReviewRepository extends BaseRepository
{
    protected string $model = Review::class;

    public function paginate(int $page = 1, int $limit = 10, string $sortBy = 'id', string $order = 'desc', array $filters = []): array
    {

        $allowedSortBy = ['id', 'rating', 'status', 'created_at'];

        if (!in_array($sortBy, $allowedSortBy)) {
            throw new \InvalidArgumentException("Invalid sortBy: $sortBy");
        }

        $order = strtolower($order) === 'asc' ? 'asc' : 'desc';

        $query = $this->model::query();

        // Search by content
        if (!empty($filters['keyword'])) {
            $keyword = trim($filters['keyword']);
            $query->where('content', 'like', "%{$keyword}%");
        }

        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['status'])) {
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
                    'sortBy'    => $sortBy,
                    'order'     => $order
                ],
            ],
        ];
    }
}
