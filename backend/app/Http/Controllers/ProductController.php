<?php

namespace App\Http\Controllers;

use Illuminate\Validation\ValidationException;

use Illuminate\Http\Request;
use App\Repositories\ProductRepository;
use Core\Response;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Validators\ProductValidator;
use Faker\Provider\Base;
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


        $result = $this->productRepository->paginate($page, $limit, $sortBy, $order, $filters);

        return Response::success($result);
    }

    /**
     * Display a single product by ID.
     */
    public function show(int $id)
    {
        $product = $this->productRepository->find($id);
        // check note loi

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
        // validate input data
        $validated = $this->validate('validateUpdate', $id);
        // find the products
        $product = $this->productRepository->find($id);
        if (!$product) {
            return Response::error('Product not found', 404);
        }
        try {
            $product->fill($validated);

            $product->save();

            return Response::success($product, 'Product updated successfully');
        } catch (Exception $e) {
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
