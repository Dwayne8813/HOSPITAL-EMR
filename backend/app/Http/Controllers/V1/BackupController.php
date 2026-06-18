<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Backup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        try {
            $filename = 'backup_' . now()->format('Y-m-d_His') . '.sql';
            $path = 'backups/' . $filename;

            Artisan::call('backup:run', [
                '--only-db' => true,
            ]);

            $backup = Backup::create([
                'institution_id' => auth()->user()->institution_id,
                'filename' => $filename,
                'path' => $path,
                'size' => Storage::disk('local')->exists($path) ? Storage::disk('local')->size($path) : 0,
                'status' => 'completed',
                'created_by' => auth()->id(),
                'created_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'data' => $backup,
                'message' => 'Backup created successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to create backup: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function download(int $id): JsonResponse
    {
        try {
            $backup = Backup::findOrFail($id);

            if (!Storage::disk('local')->exists($backup->path)) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Backup file not found.',
                ], 404);
            }

            return Storage::disk('local')->download($backup->path, $backup->filename);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Backup not found.',
            ], 404);
        }
    }

    public function restore(Request $request, int $id): JsonResponse
    {
        try {
            $backup = Backup::findOrFail($id);

            if (!Storage::disk('local')->exists($backup->path)) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message' => 'Backup file not found.',
                ], 404);
            }

            Artisan::call('backup:restore', [
                '--backup' => $backup->filename,
            ]);

            $backup->update(['restored_at' => now()]);

            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Backup restored successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Backup not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to restore backup.',
            ], 500);
        }
    }

    public function list(Request $request): JsonResponse
    {
        try {
            $backups = Backup::where('institution_id', auth()->user()->institution_id)
                ->orderByDesc('created_at')
                ->paginate($request->input('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $backups->items(),
                'message' => 'Backups retrieved successfully.',
                'meta' => [
                    'current_page' => $backups->currentPage(),
                    'last_page' => $backups->lastPage(),
                    'per_page' => $backups->perPage(),
                    'total' => $backups->total(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve backups.',
            ], 500);
        }
    }
}
