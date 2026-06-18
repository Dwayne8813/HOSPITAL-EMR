<?php

declare(strict_types=1);

namespace App\Services\Telemedicine;

use App\Models\Institution;
use App\Models\TelemedicineMessage;
use App\Models\TelemedicineSession;
use Illuminate\Http\Request;

class TelemedicineService
{
    public function createSession(array $data): TelemedicineSession
    {
        return TelemedicineSession::create([
            'institution_id' => auth()->user()->institution_id,
            'patient_id' => $data['patient_id'],
            'doctor_id' => $data['doctor_id'],
            'medical_record_id' => $data['medical_record_id'] ?? null,
            'status' => 'scheduled',
            'session_type' => $data['session_type'] ?? 'video',
            'scheduled_at' => $data['scheduled_at'] ?? now()->toDateTimeString(),
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function getSession(int $id): TelemedicineSession
    {
        return TelemedicineSession::with([
            'patient',
            'doctor',
            'medicalRecord',
            'messages.sender',
        ])->findOrFail($id);
    }

    public function listSessions(Request $request): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = TelemedicineSession::query()
            ->where('institution_id', auth()->user()->institution_id)
            ->with(['patient', 'doctor']);

        if ($request->has('patient_id')) {
            $query->where('patient_id', $request->input('patient_id'));
        }

        if ($request->has('doctor_id')) {
            $query->where('doctor_id', $request->input('doctor_id'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        return $query->orderByDesc('scheduled_at')
            ->paginate($request->input('per_page', 15));
    }

    public function updateSession(TelemedicineSession $session, array $data): TelemedicineSession
    {
        $session->update($data);
        return $session->fresh()->load(['patient', 'doctor']);
    }

    public function getMessages(int $sessionId): \Illuminate\Database\Eloquent\Collection
    {
        return TelemedicineMessage::where('telemedicine_session_id', $sessionId)
            ->with(['sender'])
            ->orderBy('created_at')
            ->get();
    }

    public function sendMessage(int $sessionId, int $senderId, string $content): TelemedicineMessage
    {
        return TelemedicineMessage::create([
            'telemedicine_session_id' => $sessionId,
            'institution_id' => auth()->user()->institution_id,
            'sender_id' => $senderId,
            'content' => $content,
            'message_type' => 'text',
        ]);
    }
}
