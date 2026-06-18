<?php

declare(strict_types=1);

namespace App\Services\Clinical;

use App\DTOs\ConsultationDTO;
use App\DTOs\EvolutionDTO;
use App\DTOs\MedicalRecordDTO;
use App\DTOs\SoapNoteDTO;
use App\DTOs\VitalSignDTO;
use App\DTOs\DiagnosisDTO;
use App\Models\Antecedent;
use App\Models\Cie10Code;
use App\Models\Consultation;
use App\Models\Diagnosis;
use App\Models\Evolution;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordAttachment;
use App\Models\PatientVitalsHistory;
use App\Models\SoapNote;
use App\Models\VitalSign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClinicalService
{
    public function createMedicalRecord(array $data): MedicalRecord
    {
        $institutionId = auth()->user()->institution_id;
        $recordNumber = $this->generateRecordNumber($institutionId);

        return DB::transaction(function () use ($data, $institutionId, $recordNumber) {
            $record = MedicalRecord::create([
                'institution_id' => $institutionId,
                'patient_id' => $data['patient_id'],
                'doctor_id' => $data['doctor_id'],
                'record_number' => $recordNumber,
                'type' => $data['type'] ?? 'consultation',
                'status' => $data['status'] ?? 'draft',
                'chief_complaint' => $data['chief_complaint'] ?? null,
                'history_of_present_illness' => $data['history_of_present_illness'] ?? null,
                'physical_examination' => $data['physical_examination'] ?? null,
                'assessment' => $data['assessment'] ?? null,
                'plan' => $data['plan'] ?? null,
                'notes' => $data['notes'] ?? null,
                'visit_date' => $data['visit_date'] ?? now()->toDateString(),
            ]);

            if (isset($data['antecedents'])) {
                Antecedent::create([
                    'medical_record_id' => $record->id,
                    'institution_id' => $institutionId,
                    'personal_pathological' => $data['antecedents']['personal_pathological'] ?? null,
                    'personal_non_pathological' => $data['antecedents']['personal_non_pathological'] ?? null,
                    'family' => $data['antecedents']['family'] ?? null,
                    'gynecological' => $data['antecedents']['gynecological'] ?? null,
                    'surgical' => $data['antecedents']['surgical'] ?? null,
                    'allergies' => $data['antecedents']['allergies'] ?? null,
                    'medications' => $data['antecedents']['medications'] ?? null,
                ]);
            }

            return $record->load(['antecedent', 'patient', 'doctor']);
        });
    }

    public function getMedicalRecord(int $id): MedicalRecord
    {
        return MedicalRecord::with([
            'patient',
            'doctor',
            'antecedent',
            'consultation',
            'soapNote',
            'diagnoses.cie10Code',
            'evolutions',
            'vitalSigns',
            'prescriptions.items.medication',
            'labOrders.items',
            'imagingOrders',
            'attachments',
        ])->findOrFail($id);
    }

    public function listMedicalRecords(Request $request): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = MedicalRecord::query()
            ->byInstitution(auth()->user()->institution_id)
            ->with(['patient', 'doctor']);

        if ($request->has('patient_id')) {
            $query->byPatient($request->input('patient_id'));
        }

        if ($request->has('doctor_id')) {
            $query->byDoctor($request->input('doctor_id'));
        }

        if ($request->has('type')) {
            $query->byType($request->input('type'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('date_from')) {
            $query->where('visit_date', '>=', $request->input('date_from'));
        }

        if ($request->has('date_to')) {
            $query->where('visit_date', '<=', $request->input('date_to'));
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('record_number', 'LIKE', "%{$search}%")
                  ->orWhere('chief_complaint', 'LIKE', "%{$search}%")
                  ->orWhereHas('patient', fn ($pq) => $pq->search($search));
            });
        }

        $sortBy = $request->input('sort_by', 'visit_date');
        $sortDirection = $request->input('sort_direction', 'desc');

        return $query->orderBy($sortBy, $sortDirection)
            ->paginate($request->input('per_page', 15));
    }

    public function updateMedicalRecord(MedicalRecord $record, array $data): MedicalRecord
    {
        $record->update($data);
        return $record->fresh()->load(['antecedent', 'patient', 'doctor', 'diagnoses']);
    }

    public function createConsultation(array $data): Consultation
    {
        return Consultation::create([
            'institution_id' => auth()->user()->institution_id,
            'medical_record_id' => $data['medical_record_id'],
            'patient_id' => $data['patient_id'],
            'doctor_id' => $data['doctor_id'],
            'anamnesis' => $data['anamnesis'] ?? null,
            'physical_examination' => $data['physical_examination'] ?? null,
            'assessment' => $data['assessment'] ?? null,
            'plan' => $data['plan'] ?? null,
            'additional_notes' => $data['additional_notes'] ?? null,
            'consultation_date' => $data['consultation_date'] ?? now()->toDateTimeString(),
        ]);
    }

    public function getConsultation(int $id): Consultation
    {
        return Consultation::with([
            'medicalRecord.patient',
            'medicalRecord.doctor',
        ])->findOrFail($id);
    }

    public function createSoapNote(array $data): SoapNote
    {
        $existing = SoapNote::where('medical_record_id', $data['medical_record_id'])->first();

        if ($existing) {
            $existing->update([
                'subjective' => $data['subjective'] ?? $existing->subjective,
                'objective' => $data['objective'] ?? $existing->objective,
                'assessment' => $data['assessment'] ?? $existing->assessment,
                'plan' => $data['plan'] ?? $existing->plan,
            ]);

            return $existing->fresh();
        }

        return SoapNote::create([
            'institution_id' => auth()->user()->institution_id,
            'medical_record_id' => $data['medical_record_id'],
            'subjective' => $data['subjective'] ?? null,
            'objective' => $data['objective'] ?? null,
            'assessment' => $data['assessment'] ?? null,
            'plan' => $data['plan'] ?? null,
            'doctor_id' => $data['doctor_id'] ?? auth()->id(),
            'note_date' => $data['note_date'] ?? now()->toDateTimeString(),
        ]);
    }

    public function getSoapNote(int $medicalRecordId): ?SoapNote
    {
        return SoapNote::where('medical_record_id', $medicalRecordId)
            ->with(['doctor'])
            ->first();
    }

    public function createEvolution(array $data): Evolution
    {
        return Evolution::create([
            'institution_id' => auth()->user()->institution_id,
            'medical_record_id' => $data['medical_record_id'],
            'doctor_id' => $data['doctor_id'] ?? auth()->id(),
            'evolution_type' => $data['evolution_type'] ?? 'clinical',
            'subjective' => $data['subjective'] ?? null,
            'objective' => $data['objective'] ?? null,
            'assessment' => $data['assessment'] ?? null,
            'plan' => $data['plan'] ?? null,
            'notes' => $data['notes'] ?? null,
            'evolution_date' => $data['evolution_date'] ?? now()->toDateTimeString(),
        ]);
    }

    public function getEvolutions(int $medicalRecordId): \Illuminate\Database\Eloquent\Collection
    {
        return Evolution::where('medical_record_id', $medicalRecordId)
            ->with(['doctor'])
            ->orderByDesc('evolution_date')
            ->get();
    }

    public function addVitalSign(array $data): VitalSign
    {
        $bmi = null;
        if ($data['weight'] && $data['height']) {
            $heightInMeters = $data['height'] / 100;
            $bmi = round($data['weight'] / ($heightInMeters * $heightInMeters), 2);
        }

        $vitalSign = VitalSign::create([
            'institution_id' => auth()->user()->institution_id,
            'medical_record_id' => $data['medical_record_id'],
            'patient_id' => $data['patient_id'],
            'temperature' => $data['temperature'] ?? null,
            'blood_pressure_systolic' => $data['blood_pressure_systolic'] ?? null,
            'blood_pressure_diastolic' => $data['blood_pressure_diastolic'] ?? null,
            'heart_rate' => $data['heart_rate'] ?? null,
            'respiratory_rate' => $data['respiratory_rate'] ?? null,
            'oxygen_saturation' => $data['oxygen_saturation'] ?? null,
            'weight' => $data['weight'] ?? null,
            'height' => $data['height'] ?? null,
            'bmi' => $bmi,
            'notes' => $data['notes'] ?? null,
            'recorded_by' => $data['recorded_by'] ?? auth()->id(),
            'recorded_at' => $data['recorded_at'] ?? now()->toDateTimeString(),
        ]);

        PatientVitalsHistory::create([
            'patient_id' => $data['patient_id'],
            'institution_id' => auth()->user()->institution_id,
            'vital_sign_id' => $vitalSign->id,
            'recorded_by' => auth()->id(),
        ]);

        return $vitalSign;
    }

    public function getVitalSigns(int $patientId, int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return VitalSign::where('patient_id', $patientId)
            ->orderByDesc('recorded_at')
            ->limit($limit)
            ->get();
    }

    public function addDiagnosis(array $data): Diagnosis
    {
        return Diagnosis::create([
            'institution_id' => auth()->user()->institution_id,
            'medical_record_id' => $data['medical_record_id'],
            'patient_id' => $data['patient_id'],
            'cie10_code_id' => $data['cie10_code_id'] ?? null,
            'diagnosis_type' => $data['diagnosis_type'] ?? 'primary',
            'description' => $data['description'],
            'notes' => $data['notes'] ?? null,
            'diagnosed_by' => $data['diagnosed_by'] ?? auth()->id(),
            'diagnosis_date' => $data['diagnosis_date'] ?? now()->toDateString(),
        ]);
    }

    public function getDiagnoses(int $medicalRecordId): \Illuminate\Database\Eloquent\Collection
    {
        return Diagnosis::where('medical_record_id', $medicalRecordId)
            ->with(['cie10Code', 'doctor'])
            ->get();
    }

    public function searchCie10(string $term): \Illuminate\Database\Eloquent\Collection
    {
        return Cie10Code::where('code', 'LIKE', "%{$term}%")
            ->orWhere('description', 'LIKE', "%{$term}%")
            ->limit(20)
            ->get();
    }

    public function getAntecedents(int $medicalRecordId): ?Antecedent
    {
        return Antecedent::where('medical_record_id', $medicalRecordId)->first();
    }

    public function updateAntecedents(int $medicalRecordId, array $data): Antecedent
    {
        $antecedent = Antecedent::where('medical_record_id', $medicalRecordId)->first();

        if (!$antecedent) {
            $antecedent = Antecedent::create([
                'medical_record_id' => $medicalRecordId,
                'institution_id' => auth()->user()->institution_id,
                ...$data,
            ]);
        } else {
            $antecedent->update($data);
        }

        return $antecedent->fresh();
    }

    public function getAttachments(int $medicalRecordId): \Illuminate\Database\Eloquent\Collection
    {
        return MedicalRecordAttachment::where('medical_record_id', $medicalRecordId)
            ->with(['uploader'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function addAttachment(int $medicalRecordId, array $fileData, int $userId): MedicalRecordAttachment
    {
        $file = $fileData['file'];
        $path = $file->store('medical-records/' . $medicalRecordId, 'public');

        return MedicalRecordAttachment::create([
            'medical_record_id' => $medicalRecordId,
            'institution_id' => auth()->user()->institution_id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'attachment_type' => $fileData['attachment_type'] ?? 'document',
            'title' => $fileData['title'] ?? $file->getClientOriginalName(),
            'description' => $fileData['description'] ?? null,
            'uploaded_by' => $userId,
        ]);
    }

    private function generateRecordNumber(int $institutionId): string
    {
        $prefix = 'MR';
        $year = now()->format('Y');
        $lastRecord = MedicalRecord::where('institution_id', $institutionId)
            ->whereYear('created_at', now()->year)
            ->orderByDesc('id')
            ->value('record_number');

        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord, -6);
            $newNumber = str_pad((string) ($lastNumber + 1), 6, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '000001';
        }

        return "{$prefix}{$year}{$newNumber}";
    }
}
