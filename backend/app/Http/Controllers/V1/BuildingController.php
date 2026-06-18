<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Building;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $buildings = Building::where('institution_id', auth()->user()->institution_id)
                ->with(['rooms'])
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $buildings,
                'message' => 'Buildings retrieved successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve buildings.',
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:buildings,code'],
            'floors' => ['nullable', 'integer', 'min:1'],
            'address' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $building = Building::create([
                'institution_id' => auth()->user()->institution_id,
                ...$request->all(),
                'is_active' => true,
            ]);

            return response()->json([
                'success' => true,
                'data' => $building,
                'message' => 'Building created successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to create building.',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $building = Building::with(['rooms.beds'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $building,
                'message' => 'Building retrieved successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Building not found.',
            ], 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'floors' => ['sometimes', 'integer', 'min:1'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        try {
            $building = Building::findOrFail($id);
            $building->update($request->all());

            return response()->json([
                'success' => true,
                'data' => $building->fresh(),
                'message' => 'Building updated successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Building not found.',
            ], 404);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $building = Building::findOrFail($id);
            $building->update(['is_active' => false]);

            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Building deactivated successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Building not found.',
            ], 404);
        }
    }
}
