<?php

namespace App\Http\Controllers;

use App\Repositories\ReviewRepository;
use App\Validators\ReviewValidator;
use Core\Controllers\BaseController;
use Core\Exceptions\bussinessException;
use Core\Response;
class ReviewController extends BaseController
{
    protected ReviewRepository $reviewRepository;

    protected string $validator = ReviewValidator::class;

    public function __construct()
    {
        $this->reviewRepository = resolve(ReviewRepository::class);
    }

    public function index()
    {
        $params = request()->all();

        $page   = (int)($params['page'] ?? 1);
        $limit  = (int)($params['limit'] ?? 10);
        $sortBy = $params['sortBy'] ?? 'id';
        $order  = $params['order'] ?? 'desc';

        $filters = [
            'keyword'    => $params['keyword'] ?? null,
            'product_id' => $params['product_id'] ?? null,
            'user_id'    => $params['user_id'] ?? null,
            'status'     => $params['status'] ?? null,
        ];

        $result = $this->reviewRepository->paginate(
            $page,
            $limit,
            $sortBy,
            $order,
            $filters
        );

        return Response::success($result);
    }

    public function show(int $id)
    {
        $review = $this->reviewRepository->find($id);

        return $review
            ? Response::success($review)
            : Response::error('Review not found', 404);
    }

    public function store()
    {
        $validated = $this->validate('validateCreate');

        $review = $this->reviewRepository->save($validated);

        return Response::success($review, 'Review created successfully');
    }

    public function update(int $id)
    {
        $review = $this->reviewRepository->find($id);

        if (!$review) {
            return Response::error('Review not found', 404);
        }

        $validated = $this->validate('validateUpdate', $id);

        try {
            $review->fill($validated);
            $review->save();

            return Response::success($review, 'Review updated successfully');
        } catch (BussinessException $e) {
            return Response::error('Conflict detected, please refresh.', 409);
        }
    }

    public function destroy(int $id)
    {
        $review = $this->reviewRepository->find($id);

        if (!$review) {
            return Response::error('Review not found', 404);
        }

        $this->reviewRepository->delete($id);

        return Response::success(null, 'Review deleted successfully');
    }
}
