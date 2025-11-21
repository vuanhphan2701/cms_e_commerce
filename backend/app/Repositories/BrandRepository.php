<?php

namespace App\Repositories;

use App\Models\Brand;
use Core\Repositories\BaseRepository;

class BrandRepository extends BaseRepository
{
    protected string $model = Brand::class;

    public function paginate(int $page, int $limit, string $sortBy, string $order, array $filters)
    {
        $query = $this->model::query();

        if (!empty($filters['keyword'])) {
            $keyword = trim($filters['keyword']);
            $query->where('name', 'like', "%$keyword%")
                ->orWhere('alias', 'like', "%$keyword%");
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
