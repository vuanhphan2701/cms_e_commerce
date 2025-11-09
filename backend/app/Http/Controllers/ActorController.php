<?php

namespace App\Http\Controllers;

use App\Models\Actor;
use App\Validators\ActorValidator;
use Core\controllers\BaseController;
use Illuminate\Http\Request;

class ActorController extends BaseController
{
    protected string $validator = ActorValidator::class;

    /**
     * Lấy danh sách actor
     */
    public function index()
    {
        return response()->json(Actor::all());
    }

    /**
     * Tạo actor mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $actor = Actor::create([
            'name' => $request->name,
        ]);

        return response()->json($actor, 201);
    }

    /**
     * Update actor (test Optimistic Locking)
     */
    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {

        $this->validate('validateCreate');

        $actor = Actor::findOrFail($id);

        $actor->name = $request->name;

        try {
            $actor->save();
            return response()->json([
                'message' => 'Updated successfully',
                'actor' => $actor->refresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Conflict detected: ' . $e->getMessage(),
            ], 409);
        }
    }
}
