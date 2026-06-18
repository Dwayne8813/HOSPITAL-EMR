<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Services\Pharmacy\PharmacyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $suppliers = Supplier::where('institution_id', auth()->user()->institution_id)
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $suppliers,
                'message' => 'Suppliers retrieved successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve suppliers.',
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'nit' => ['nullable', 'string', 'max:50'],
        ]);

        try {
            $supplier = Supplier::create([
                'institution_id' => auth()->user()->institution_id,
                'name' => $request->name,
                'contact_person' => $request->contact_person,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'nit' => $request->nit,
                'status' => 'active',
            ]);

            return response()->json([
                'success' => true,
                'data' => $supplier,
                'message' => 'Supplier created successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to create supplier.',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $supplier = Supplier::with(['purchaseOrders'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $supplier,
                'message' => 'Supplier retrieved successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Supplier not found.',
            ], 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'nit' => ['nullable', 'string'],
            'status' => ['sometimes', 'string'],
        ]);

        try {
            $supplier = Supplier::findOrFail($id);
            $supplier->update($request->only([
                'name', 'contact_person', 'email', 'phone', 'address', 'nit', 'status'
            ]));

            return response()->json([
                'success' => true,
                'data' => $supplier->fresh(),
                'message' => 'Supplier updated successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Supplier not found.',
            ], 404);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $supplier->update(['status' => 'inactive']);

            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Supplier deactivated successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Supplier not found.',
            ], 404);
        }
    }
}
