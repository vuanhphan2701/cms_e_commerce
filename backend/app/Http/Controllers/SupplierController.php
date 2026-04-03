<?php

namespace App\Http\Controllers;

use App\Repositories\SupplierRepository;
use App\Validators\SupplierValidator;
use Core\Controllers\BaseController;
use Core\Exceptions\BussinessException;
use Core\Response;
use Illuminate\Http\Request;

class SupplierController extends BaseController
{
    protected SupplierRepository $supplierRepository;

    protected string $validator = SupplierValidator::class;

    public function __construct()
    {
        $this->supplierRepository = resolve(SupplierRepository::class);
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

        $include = [];

        // fetch related entities if specified
        if ($request->has('include')) {
            $include = explode(',', $request->include);
        }

        $result = $this->supplierRepository
            ->paginate($page, $limit, $sortBy, $order, $filters, $include);

        return Response::success($result);
    }

    public function show(int $id)
    {
        $include = [];

        // fetch related entities if specified
        if (request()->has('include')) {
            $include = explode(',', request()->include);
        }

        $supplier = $this->supplierRepository->find($id, $include);

        return $supplier
            ? Response::success($supplier)
            : Response::error('Supplier not found', 404);
    }

    public function store()
    {
        $validated = $this->validate('validateCreate');

        $supplier = $this->supplierRepository->save($validated);

        return Response::success($supplier, 'Supplier created successfully');
    }

    public function update(int $id)
    {
        $supplier = $this->supplierRepository->find($id);

        if (!$supplier) {
            return Response::error('Supplier not found', 404);
        }

        $validated = $this->validate('validateUpdate', $id);

        try {
            $supplier->fill($validated);
            $supplier->save();

            return Response::success($supplier, 'Supplier updated successfully');
        } catch (BussinessException $e) {
            return Response::error('Conflict detected. Please refresh and try again.', 409);
        }
    }

    public function destroy(int $id)
    {
        $supplier = $this->supplierRepository->find(id: $id);

        if (!$supplier) {
            return Response::error('Supplier not found', 404);
        }

        $this->supplierRepository->delete($id);

        return Response::success(null, 'Supplier deleted successfully');
    }
}
