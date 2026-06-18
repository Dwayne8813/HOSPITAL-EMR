<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $settings = Setting::where('institution_id', auth()->user()->institution_id)
                ->pluck('value', 'key')
                ->toArray();

            return response()->json([
                'success' => true,
                'data' => $settings,
                'message' => 'Settings retrieved successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve settings.',
            ], 500);
        }
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'settings' => ['required', 'array'],
            'settings.*' => ['nullable', 'string'],
        ]);

        try {
            $institutionId = auth()->user()->institution_id;

            foreach ($request->input('settings') as $key => $value) {
                Setting::updateOrCreate(
                    [
                        'institution_id' => $institutionId,
                        'key' => $key,
                    ],
                    [
                        'value' => $value,
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Settings updated successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to update settings.',
            ], 500);
        }
    }
}
