<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = User::where('institution_id', auth()->user()->institution_id)
                ->with(['role', 'specialty']);

            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
            }

            if ($request->has('role_id')) {
                $query->where('role_id', $request->input('role_id'));
            }

            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            $users = $query->orderBy('name')
                ->paginate($request->input('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $users->items(),
                'message' => 'Users retrieved successfully.',
                'meta' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve users.',
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
            'specialty_id' => ['nullable', 'integer', 'exists:specialties,id'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        try {
            $user = User::create([
                'institution_id' => auth()->user()->institution_id,
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'role_id' => $request->input('role_id'),
                'specialty_id' => $request->input('specialty_id'),
                'phone' => $request->input('phone'),
                'is_active' => true,
            ]);

            return response()->json([
                'success' => true,
                'data' => $user->load(['role', 'specialty']),
                'message' => 'User created successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to create user.',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $user = User::with(['role', 'specialty', 'institution'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'User retrieved successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'User not found.',
            ], 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:users,email,' . $id],
            'role_id' => ['sometimes', 'integer', 'exists:roles,id'],
            'specialty_id' => ['nullable', 'integer'],
            'phone' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        try {
            $user = User::findOrFail($id);
            $data = $request->except(['password', 'password_confirmation']);

            if ($request->has('password') && $request->input('password')) {
                $data['password'] = Hash::make($request->input('password'));
            }

            $user->update($data);

            return response()->json([
                'success' => true,
                'data' => $user->fresh()->load(['role', 'specialty']),
                'message' => 'User updated successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'User not found.',
            ], 404);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Cannot delete your own account.',
                ], 422);
            }

            $user->update(['is_active' => false]);

            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'User deactivated successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'User not found.',
            ], 404);
        }
    }
}
