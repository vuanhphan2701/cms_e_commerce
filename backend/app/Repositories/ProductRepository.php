<?php

namespace App\Repositories;

use App\Models\Product;
use Core\Repositories\BaseRepository;

class ProductRepository extends BaseRepository
{
    protected string $model = Product::class;

    public function paginate(int $page = 1, int $limit = 10): array
    {
        $paginator = Product::orderBy('id', 'desc')->paginate($limit, ['*'], 'page', $page);

        return [
            'data' => $paginator->items(),
            'meta' => [
                'pagination' => [
                    'page' => $paginator->currentPage(),
                    'pageSize' => $paginator->perPage(),
                    'pageCount' => $paginator->lastPage(),
                    'total' => $paginator->total(),
                ],
            ],
        ];
    }
}
