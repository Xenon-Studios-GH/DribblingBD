<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderDraft;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderDraftController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = OrderDraft::where('user_id', Auth::id());

        if ($request->filled('order_id')) {
            $query->where('order_id', $request->integer('order_id'));
        } else {
            $query->whereNull('order_id');
        }

        return response()->json($query->orderBy('updated_at', 'desc')->get());
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'order_id' => 'nullable|integer|exists:orders,id',
            'data' => 'required|json',
        ]);

        $draft = OrderDraft::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'order_id' => $data['order_id'] ?? null,
            ],
            [
                'data' => json_decode($data['data'], true),
            ]
        );

        return response()->json($draft);
    }

    public function show(OrderDraft $orderDraft): JsonResponse
    {
        abort_if($orderDraft->user_id !== Auth::id(), 403);
        return response()->json($orderDraft);
    }

    public function destroy(OrderDraft $orderDraft): JsonResponse
    {
        abort_if($orderDraft->user_id !== Auth::id(), 403);
        $orderDraft->delete();
        return response()->json(['message' => 'Draft deleted.']);
    }
}
