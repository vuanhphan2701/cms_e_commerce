<?php

namespace App\Repositories;

use App\Models\Brand;
use Core\Repositories\BaseRepository;

class BrandRepository extends BaseRepository
{
    protected string $model = Brand::class;

    public function paginate(int $page, int $limit, string $sortBy, string $order, array $filters, array $include = []): array
    {
        $query = $this->model::query()->select('brands.*');

        // fetch products of brands
        if(!empty($include)){
            $query->with($include);
        }
         // Allowed sorting fields

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

    public function find($id, array $include = []): mixed
    {
        $query = $this->model::query();

        // fetch product of brands
        if(!empty($include)){
            $query->with($include);
        }

        return $query->find($id);
    }
}
