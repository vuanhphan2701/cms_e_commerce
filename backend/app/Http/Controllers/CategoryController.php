<?php

namespace App\Http\Controllers;

use App\Repositories\CategoryRepository;
use App\Validators\CategoryValidator;
use Core\Controllers\BaseController;
use Core\Exceptions\BussinessException;
use Core\Response;
use Illuminate\Http\Request;

class CategoryController extends BaseController
{
    protected CategoryRepository $categoryRepository;

    protected string $validator = CategoryValidator::class;

    public function __construct()
    {
        $this->categoryRepository = resolve(CategoryRepository::class);
    }

    public function index(Request $request)
    {
        $params = $request->all();

        $page   = (int)($params['page'] ?? 1);
        $limit  = (int)($params['limit'] ?? 10);
        $sortBy = $params['sortBy'] ?? 'id';
        $order  = $params['order'] ?? 'desc';

        $filters = [
            'keyword' => $params['keyword'] ?? null,
            'status'  => $params['status'] ?? null,
        ];

        $result = $this->categoryRepository->paginate(
            $page, $limit, $sortBy, $order, $filters
        );

        return Response::success($result);
    }

    public function show(int $id)
    {
        $category = $this->categoryRepository->find($id);

        return $category
            ? Response::success($category)
            : Response::error('Category not found', 404);
    }

    public function store()
    {
        $validated = $this->validate('validateCreate');

        $category = $this->categoryRepository->save($validated);

        return Response::success($category, 'Category created successfully', 201);
    }

    public function update(int $id)
    {
        $category = $this->categoryRepository->find($id);

        if (!$category) {
            return Response::error('Category not found', 404);
        }

        // Validate
        $validated = $this->validate('validateUpdate', $id);

        try {
            $category->fill($validated);
            $category->save();

            return Response::success($category, 'Category updated successfully');
        }
        catch (BussinessException $e) {
            return Response::error(
                'Version conflict. Refresh and try again.',
                409
            );
        }
    }

    public function destroy(int $id)
    {
        $category = $this->categoryRepository->find($id);

        if (!$category) {
            return Response::error('Category not found', 404);
        }

        $this->categoryRepository->delete($id);

        return Response::success(null, 'Category deleted successfully');
    }
}
