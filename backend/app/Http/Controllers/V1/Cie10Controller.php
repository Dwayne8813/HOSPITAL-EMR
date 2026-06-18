<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Cie10Code;
use App\Services\Clinical\ClinicalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Cie10Controller extends Controller
{
    public function __construct(
        private readonly ClinicalService $clinicalService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        try {
            $codes = Cie10Code::query()
                ->with(['category'])
                ->orderBy('code')
                ->paginate($request->input('per_page', 50));

            return response()->json([
                'success' => true,
                'data' => $codes->items(),
                'message' => 'CIE-10 codes retrieved successfully.',
                'meta' => [
                    'current_page' => $codes->currentPage(),
                    'last_page' => $codes->lastPage(),
                    'per_page' => $codes->perPage(),
                    'total' => $codes->total(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve CIE-10 codes.',
            ], 500);
        }
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'term' => ['required', 'string', 'min:2'],
        ]);

        try {
            $results = $this->clinicalService->searchCie10($request->input('term'));

            return response()->json([
                'success' => true,
                'data' => $results,
                'message' => 'Search results retrieved.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Search failed.',
            ], 500);
        }
    }
}
