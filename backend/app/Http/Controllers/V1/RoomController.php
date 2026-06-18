<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Room::where('institution_id', auth()->user()->institution_id)
                ->with(['floor', 'beds']);

            if ($request->has('building_id')) {
                $query->whereHas('floor', function ($q) use ($request) {
                    $q->where('building_id', $request->input('building_id'));
                });
            }

            if ($request->has('type')) {
                $query->where('type', $request->input('type'));
            }

            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            $rooms = $query->orderBy('number')->get();

            return response()->json([
                'success' => true,
                'data' => $rooms,
                'message' => 'Rooms retrieved successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve rooms.',
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'building_id' => ['required', 'integer', 'exists:buildings,id'],
            'number' => ['required', 'string', 'max:50'],
            'floor' => ['nullable', 'integer'],
            'type' => ['nullable', 'string'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $room = Room::create([
                'institution_id' => auth()->user()->institution_id,
                ...$request->all(),
                'status' => 'available',
            ]);

            return response()->json([
                'success' => true,
                'data' => $room,
                'message' => 'Room created successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to create room.',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $room = Room::with(['building', 'beds.hospitalization.patient'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $room,
                'message' => 'Room retrieved successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Room not found.',
            ], 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'number' => ['sometimes', 'string', 'max:50'],
            'floor' => ['nullable', 'integer'],
            'type' => ['nullable', 'string'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'status' => ['sometimes', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $room = Room::findOrFail($id);
            $room->update($request->all());

            return response()->json([
                'success' => true,
                'data' => $room->fresh(),
                'message' => 'Room updated successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Room not found.',
            ], 404);
        }
    }
}
