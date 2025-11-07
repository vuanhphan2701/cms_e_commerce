<?php

namespace App\Http\Controllers;

use Illuminate\Validation\ValidationException;

use Illuminate\Http\Request;
use App\Repositories\ProductRepository;
use Core\Response;
use Illuminate\Support\Facades\Validator;
use Exception;

/**
 * Class ProductController
 *
 * Handles all CRUD operations for products.
 * Uses the ProductRepository for data access.
 */
class ProductController extends Controller
{
    protected ProductRepository $productRepository;

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
    public function index()
    {
        $products = $this->productRepository->all();
        return Response::success($products);
    }

    /**
     * Display a single product by ID.
     */
    public function show(int $id)
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return Response::error('Product not found', code: 404);
        }

        return Response::success($product);
    }

    /**
     * Create a new product.
     */
    public function store(Request $request)
    {

        try {
            // Validate input data
            $validated = $request->validate([
                'sku'            => 'required|string|max:225|unique:products,sku',
                'name'           => 'required|string|max:255',
                'price'          => 'required|integer|min:0',
                'quantity'       => 'nullable|integer|min:0',
                'content'        => 'nullable|string',
                'summary'        => 'nullable|string',
                'image'          => 'nullable|string|max:255',
                'images'         => 'nullable|string',
                'average_rating' => 'nullable|numeric|min:0|',
                'description'    => 'nullable|string',
                'alias'          => 'nullable|string|max:255|',
                'status'         => 'nullable|integer|in:0,1',
                'category_id'    => 'nullable|integer|',
                'brand_id'       => 'nullable|integer|',
                'supplier_id'    => 'nullable|integer|',
            ]);

            $product = $this->productRepository->save($validated);

            return Response::success($product, 'Product created successfully', 201);
        } catch (ValidationException $e) {
            // manually handle validation failure
            return Response::error($e->validator->errors()->first(), 422);
        }
    }

    /**
     * Update an existing product.
     */
    public function update(Request $request, int $id)
    {
        // Manually validate to control response format
        $validator = Validator::make($request->all(), [
            'sku'            => 'required|string|max:225|unique:products,sku,' . $id,
            'name'           => 'required|string|max:255',
            'price'          => 'required|integer|min:0',
            'quantity'       => 'nullable|integer|min:0',
            'content'        => 'nullable|string',
            'summary'        => 'nullable|string',
            'image'          => 'nullable|string|max:255',
            'images'         => 'nullable|string',
            'average_rating' => 'nullable|numeric|min:0',
            'description'    => 'nullable|string',
            'alias'          => 'nullable|string|max:255|',
            'status'         => 'nullable|integer|in:0,1,2,3',
            'category_id'    => 'nullable|integer',
            'brand_id'       => 'nullable|integer',
            'supplier_id'    => 'nullable|integer',
            'version'        => 'required|integer|min:1',
        ]);

        // Return validation errors in custom format
        if ($validator->fails()) {
            return Response::error($validator->errors()->first(), 422);
        }

        $validated = $validator->validated();

        try {
            $product = $this->productRepository->find($id);
            if (!$product) {
                return Response::error('Product not found', 404);
            }

            // Optimistic locking check
            if ($product->version != $validated['version']) {
                return Response::error(
                    'Product has been modified by another user. Please refresh and try again.',
                    409
                );
            }

            // Increment version for next update
            $validated['version'] = $validated['version'] + 1;

            $updated = $this->productRepository->update($id, $validated);

            return Response::success($updated, 'Product updated successfully');
        } catch (Exception $e) {
            return Response::error('Failed to update product: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete a product.
     */
    public function destroy(int $id)
    {
        try {
            $this->productRepository->delete($id);

            return Response::success(null, 'Product deleted successfully');
        } catch (Exception $e) {

            return Response::error('Failed to delete product: ' . $e->getMessage(), 500);
        }
    }
}
