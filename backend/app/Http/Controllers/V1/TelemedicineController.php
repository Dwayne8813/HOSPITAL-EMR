<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\TelemedicineSession;
use App\Services\Telemedicine\TelemedicineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TelemedicineController extends Controller
{
    public function __construct(
        private readonly TelemedicineService $telemedicineService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $result = $this->telemedicineService->listSessions($request);

            return response()->json([
                'success' => true,
                'data' => $result->items(),
                'message' => 'Telemedicine sessions retrieved successfully.',
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
                'message' => 'Failed to retrieve telemedicine sessions.',
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'doctor_id' => ['required', 'integer', 'exists:users,id'],
            'medical_record_id' => ['nullable', 'integer', 'exists:medical_records,id'],
            'session_type' => ['nullable', 'string', 'in:video,audio,chat'],
            'scheduled_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $session = $this->telemedicineService->createSession($request->all());

            return response()->json([
                'success' => true,
                'data' => $session->load(['patient', 'doctor']),
                'message' => 'Telemedicine session created successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to create telemedicine session.',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $session = $this->telemedicineService->getSession($id);

            return response()->json([
                'success' => true,
                'data' => $session,
                'message' => 'Telemedicine session retrieved successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Telemedicine session not found.',
            ], 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => ['sometimes', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $session = TelemedicineSession::findOrFail($id);
            $updated = $this->telemedicineService->updateSession($session, $request->all());

            return response()->json([
                'success' => true,
                'data' => $updated,
                'message' => 'Telemedicine session updated successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Telemedicine session not found.',
            ], 404);
        }
    }

    public function messages(int $id): JsonResponse
    {
        try {
            $messages = $this->telemedicineService->getMessages($id);

            return response()->json([
                'success' => true,
                'data' => $messages,
                'message' => 'Messages retrieved successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve messages.',
            ], 500);
        }
    }

    public function sendMessage(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'content' => ['required', 'string', 'max:5000'],
        ]);

        try {
            $message = $this->telemedicineService->sendMessage(
                $id,
                auth()->id(),
                $request->input('content')
            );

            return response()->json([
                'success' => true,
                'data' => $message->load(['sender']),
                'message' => 'Message sent successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to send message.',
            ], 500);
        }
    }
}
