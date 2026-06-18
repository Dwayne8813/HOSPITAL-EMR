<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Bed;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BedController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Bed::where('institution_id', auth()->user()->institution_id)
                ->with(['room.floor']);

            if ($request->has('room_id')) {
                $query->where('room_id', $request->input('room_id'));
            }

            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            $beds = $query->orderBy('number')->get();

            return response()->json([
                'success' => true,
                'data' => $beds,
                'message' => 'Beds retrieved successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve beds.',
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'number' => ['required', 'string', 'max:50'],
            'type' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $bed = Bed::create([
                'institution_id' => auth()->user()->institution_id,
                ...$request->all(),
                'status' => 'available',
            ]);

            return response()->json([
                'success' => true,
                'data' => $bed,
                'message' => 'Bed created successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to create bed.',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $bed = Bed::with(['room.building', 'hospitalization.patient'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $bed,
                'message' => 'Bed retrieved successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Bed not found.',
            ], 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'number' => ['sometimes', 'string', 'max:50'],
            'type' => ['nullable', 'string'],
            'status' => ['sometimes', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $bed = Bed::findOrFail($id);
            $bed->update($request->all());

            return response()->json([
                'success' => true,
                'data' => $bed->fresh(),
                'message' => 'Bed updated successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Bed not found.',
            ], 404);
        }
    }

    public function bedMap(Request $request): JsonResponse
    {
        try {
            $beds = Bed::where('institution_id', $request->user()->institution_id)
                ->with(['room.floor'])
                ->get();

            $bedMap = $beds->groupBy('status')->map(function ($items, $status) {
                return [
                    'status' => $status,
                    'count' => $items->count(),
                    'beds' => $items->map(fn($bed) => [
                        'id' => $bed->id,
                        'number' => $bed->number,
                        'type' => $bed->type,
                        'room' => $bed->room?->name,
                        'floor' => $bed->room?->floor?->name,
                    ]),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $bedMap,
                'message' => 'Bed map retrieved successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve bed map.',
            ], 500);
        }
    }
}
