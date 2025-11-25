<?php

namespace App\Http\Controllers;

use Core\Exceptions\BussinessException;
use Illuminate\Http\Request;
use App\Repositories\ProductRepository;
use Core\Response;
use App\Validators\ProductValidator;
use Core\Controllers\BaseController;

/**
 * Class ProductController
 *
 * Handles all CRUD operations for products.
 * Uses the ProductRepository for data access.
 */
class ProductController extends BaseController
{

    /**
     * use productRepository
     * @var ProductRepository
     */
    protected ProductRepository $productRepository;

    /**
     * The validator class associated with the controller
     */
    protected string $validator = ProductValidator::class;

    /**
     * Inject the ProductRepository.
     */
    public function __construct()
    {
        // Resolve allows the repository to be auto-instantiated from the service container
        $this->productRepository = resolve(ProductRepository::class);
    }

    /**
     * Display a listing of all products.
     */
    public function index(Request $request)
    {
        $params =  $request->all();

        // Normalize pagination/sorting defaults
        $page   = (int)($params['page'] ?? 1);
        $limit  = (int)($params['limit'] ?? 10);
        $sortBy = $params['sortBy'] ?? 'id';
        $order  = $params['order'] ?? 'asc';

        // Everything else considered as filters
        $filters = [
            'keyword'     => $params['keyword']     ?? null,
            'category_id' => $params['category_id'] ?? null,
            'brand_id'    => $params['brand_id']    ?? null,
            'supplier_id' => $params['supplier_id'] ?? null,
            'status'      => $params['status']      ?? null,
        ];

        // Include related entities if specified
        $include = [];
        if ($request->has('include')) {
            $include = explode(',', $request->include);
        }

        $result = $this->productRepository->paginate(
            $page,
            $limit,
            $sortBy,
            $order,
            $filters,
            $include
        );

        return Response::success($result);
    }

    /**
     * Display a single product by ID.
     */
    public function show(int $id)
    {
        $include = [];

        if (request()->has('include')) {
            $include = explode(',', request()->include);
        }

        $product = $this->productRepository->find($id, $include);

        return $product
            ? Response::success($product)
            : Response::error('Product not found', 404);
    }

    /**
     * Create a new product.
     */
    public function store()
    {
        // Validate input data
        $validated = $this->validate('validateCreate');

        // dd($validated);
        $product = $this->productRepository->save($validated);

        return Response::success($product, 'Product created successfully');
    }

    /**
     * Update an existing product.
     */
    public function update(int $id, Request $request)
    {
        // find the products
        $product = $this->productRepository->find($id);
        if (!$product) {
            return Response::error('Product not found', 404);
        }

        // validate input data
        $validated = $this->validate('validateUpdate', $id);

        try {
            // update the product
            $product->fill($validated);

            $product->save();

            return Response::success($product, 'Product updated successfully');
        }
        // catch OptimisticLockException
        catch (BussinessException $e) {

            return Response::error('Conflict detected. Please refresh and try again.', 409);
        }
    }


    /**
     * Delete a product.
     */
    public function destroy(int $id)
    {
        $product = $this->productRepository->find(id: $id);

        if (!$product) {
            return Response::error('Product not found', 404);
        }

        $this->productRepository->delete($id);
        return Response::success(null, 'Product deleted successfully');
    }
}
