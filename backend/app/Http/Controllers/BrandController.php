<?php

namespace App\Http\Controllers;

use Core\Controllers\BaseController;
use Core\Response;
use App\Repositories\BrandRepository;
use App\Validators\BrandValidator;
use Core\Exceptions\BussinessException;
use Illuminate\Http\Request;

class BrandController extends BaseController
{
    protected BrandRepository $brandRepository;

    protected string $validator = BrandValidator::class;

    public function __construct()
    {
        $this->brandRepository = resolve(BrandRepository::class);
    }

    public function index(Request $request)
    {
        $params = request()->all();

        $page   = (int)($params['page'] ?? 1);
        $limit  = (int)($params['limit'] ?? 10);
        $sortBy = $params['sortBy'] ?? 'id';
        $order  = $params['order'] ?? 'asc';

        $filters = [
            'keyword' => $params['keyword'] ?? null,
            'status'  => $params['status'] ?? null,
        ];

        // fetch products of brands
        $include = [];
        if ($request->has('include')) {
            $include = explode(',', $request->query('include'));
        }
        $result = $this->brandRepository->paginate($page, $limit, $sortBy, $order, $filters, $include);

        return Response::success($result);
    }

    public function show(int $id)
    {
        // fetch product of brands
        $include = [];
        if (request()->has('include')) {
            $include = explode(',', request()->include);
        }
        $brand = $this->brandRepository->find($id, $include);

        return $brand
            ? Response::success($brand)
            : Response::error('Brand not found', 404);
    }

    public function store()
    {
        $validated = $this->validate('validateCreate');

        $brand = $this->brandRepository->save($validated);

        return Response::success($brand, 'Brand created successfully');
    }

    public function update(int $id)
    {
        $brand = $this->brandRepository->find($id);
        if (!$brand) {
            return Response::error('Brand not found', 404);
        }

        $validated = $this->validate('validateUpdate', $id);

        try {
            $brand->fill($validated);
            $brand->save();

            return Response::success($brand, 'Brand updated successfully');
        } catch (BussinessException $e) {
            return Response::error('Conflict detected. Please refresh and try again.', 409);
        }
    }

    public function destroy(int $id)
    {
        $brand = $this->brandRepository->find($id);

        if (!$brand) {
            return Response::error('Brand not found', 404);
        }

        $this->brandRepository->delete($id);

        return Response::success(null, 'Brand deleted successfully');
    }
}
