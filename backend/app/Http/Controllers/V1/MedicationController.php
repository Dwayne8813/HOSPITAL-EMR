<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Medication;
use App\Services\Pharmacy\PharmacyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MedicationController extends Controller
{
    public function __construct(
        private readonly PharmacyService $pharmacyService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $institutionId = $request->user()->institution_id;
            $query = \App\Models\Medication::query()
                ->byInstitution($institutionId)
                ->with(['catalog', 'category']);

            if ($request->has('search') && $request->input('search') !== '') {
                $query->search($request->input('search'));
            }

            if ($request->has('category_id')) {
                $query->where('category_id', $request->input('category_id'));
            }

            if ($request->boolean('low_stock')) {
                $query->lowStock();
            }

            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            $perPage = $request->input('per_page', 15);
            $result = $query->orderBy('name')->paginate($perPage);

            // If no medications in inventory, return from catalog instead
            if ($result->total() === 0) {
                $catalogQuery = \App\Models\MedicationCatalog::query();
                if ($request->has('search') && $request->input('search') !== '') {
                    $s = $request->input('search');
                    $catalogQuery->where(function ($q) use ($s) {
                        $q->where('name', 'LIKE', "%{$s}%")
                          ->orWhere('generic_name', 'LIKE', "%{$s}%");
                    });
                }
                $catalogResult = $catalogQuery->orderBy('name')->paginate($perPage);
                return response()->json([
                    'success' => true,
                    'data' => $catalogResult->items(),
                    'message' => 'Medications retrieved from catalog.',
                    'meta' => [
                        'current_page' => $catalogResult->currentPage(),
                        'last_page' => $catalogResult->lastPage(),
                        'per_page' => $catalogResult->perPage(),
                        'total' => $catalogResult->total(),
                    ],
                ], 200);
            }

            return response()->json([
                'success' => true,
                'data' => $result->items(),
                'message' => 'Medications retrieved successfully.',
                'meta' => [
                    'current_page' => $result->currentPage(),
                    'last_page' => $result->lastPage(),
                    'per_page' => $result->perPage(),
                    'total' => $result->total(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve medications.',
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'generic_name' => ['required', 'string', 'max:255'],
            'catalog_id' => ['nullable', 'integer', 'exists:medication_catalogs,id'],
            'category_id' => ['nullable', 'integer', 'exists:medication_categories,id'],
            'concentration' => ['nullable', 'string', 'max:100'],
            'pharmaceutical_form' => ['nullable', 'string', 'max:100'],
            'manufacturer' => ['nullable', 'string', 'max:255'],
            'requires_prescription' => ['nullable', 'boolean'],
            'is_controlled' => ['nullable', 'boolean'],
            'minimum_stock' => ['nullable', 'integer', 'min:0'],
            'current_stock' => ['nullable', 'integer', 'min:0'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            $medication = $this->pharmacyService->createMedication($request->all());

            return response()->json([
                'success' => true,
                'data' => $medication,
                'message' => 'Medication created successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to create medication.',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $medication = Medication::with(['catalog', 'category', 'batches'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $medication,
                'message' => 'Medication retrieved successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Medication not found.',
            ], 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'generic_name' => ['sometimes', 'string', 'max:255'],
            'concentration' => ['nullable', 'string'],
            'pharmaceutical_form' => ['nullable', 'string'],
            'manufacturer' => ['nullable', 'string'],
            'requires_prescription' => ['sometimes', 'boolean'],
            'is_controlled' => ['sometimes', 'boolean'],
            'minimum_stock' => ['sometimes', 'integer', 'min:0'],
            'current_stock' => ['sometimes', 'integer', 'min:0'],
            'unit_price' => ['sometimes', 'numeric', 'min:0'],
            'cost_price' => ['sometimes', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        try {
            $medication = Medication::findOrFail($id);
            $updated = $this->pharmacyService->update($medication, $request->all());

            return response()->json([
                'success' => true,
                'data' => $updated,
                'message' => 'Medication updated successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Medication not found.',
            ], 404);
        }
    }

    public function lowStock(Request $request): JsonResponse
    {
        try {
            $medications = $this->pharmacyService->getLowStock($request->user()->institution);

            return response()->json([
                'success' => true,
                'data' => $medications,
                'message' => 'Low stock medications retrieved.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve low stock medications.',
            ], 500);
        }
    }

    public function expiring(Request $request): JsonResponse
    {
        $request->validate([
            'days' => ['nullable', 'integer', 'min:1', 'max:365'],
        ]);

        try {
            $batches = $this->pharmacyService->getExpiringSoon(
                $request->user()->institution,
                $request->input('days', 30)
            );

            return response()->json([
                'success' => true,
                'data' => $batches,
                'message' => 'Expiring medications retrieved.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve expiring medications.',
            ], 500);
        }
    }
}
