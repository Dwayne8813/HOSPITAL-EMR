<?php

declare(strict_types=1);

namespace App\Services\Patient;

use App\DTOs\PatientDTO;
use App\Models\Institution;
use App\Models\Patient;
use App\Models\PatientAllergy;
use App\Models\PatientContact;
use App\Models\PatientDocument;
use App\Models\PatientInsurance;
use App\Repositories\Interfaces\PatientRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PatientService
{
    public function __construct(
        private readonly PatientRepositoryInterface $patientRepository,
    ) {
    }

    public function create(array $data): Patient
    {
        $institutionId = auth()->user()->institution_id;

        $internalCode = $this->generateInternalCode($institutionId);
        $medicalRecordNumber = $this->generateMedicalRecordNumber($institutionId);

        return DB::transaction(function () use ($data, $institutionId, $internalCode, $medicalRecordNumber) {
            return Patient::create([
                'institution_id' => $institutionId,
                'internal_code' => $internalCode,
                'medical_record_number' => $medicalRecordNumber,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'document_type' => $data['document_type'],
                'document_number' => $data['document_number'],
                'birth_date' => $data['birth_date'],
                'gender' => $data['gender'],
                'blood_type' => $data['blood_type'] ?? null,
                'marital_status' => $data['marital_status'] ?? null,
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'city_id' => $data['city_id'] ?? null,
                'department_id' => $data['department_id'] ?? null,
                'occupation' => $data['occupation'] ?? null,
                'photo' => $data['photo'] ?? null,
                'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
                'emergency_contact_relationship' => $data['emergency_contact_relationship'] ?? null,
                'status' => 'active',
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }

    public function update(Patient $patient, array $data): Patient
    {
        $patient->update($data);
        return $patient->fresh();
    }

    public function getById(int $id): Patient
    {
        return Patient::with([
            'institution',
            'city',
            'department',
            'doctor',
            'contacts',
            'documents',
            'allergies',
            'insurances',
        ])->findOrFail($id);
    }

    public function list(Request $request): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Patient::query()
            ->byInstitution(auth()->user()->institution_id)
            ->with(['city', 'department', 'doctor']);

        if ($request->has('search')) {
            $query->search($request->input('search'));
        }

        if ($request->has('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        if ($request->has('gender')) {
            $query->where('gender', $request->input('gender'));
        }

        if ($request->has('blood_type')) {
            $query->where('blood_type', $request->input('blood_type'));
        }

        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        return $query->orderBy($sortBy, $sortDirection)
            ->paginate($request->input('per_page', 15));
    }

    public function search(string $term): \Illuminate\Support\Collection
    {
        return Patient::byInstitution(auth()->user()->institution_id)
            ->search($term)
            ->limit(20)
            ->get();
    }

    public function softDelete(Patient $patient): void
    {
        $patient->delete();
    }

    public function restore(int $id): Patient
    {
        $patient = Patient::onlyTrashed()->findOrFail($id);
        $patient->restore();
        return $patient;
    }

    public function getMedicalHistory(Patient $patient): \Illuminate\Database\Eloquent\Collection
    {
        return $patient->medicalRecords()
            ->with(['doctor', 'diagnoses', 'consultation', 'prescriptions'])
            ->orderByDesc('visit_date')
            ->get();
    }

    public function getDocuments(Patient $patient): \Illuminate\Database\Eloquent\Collection
    {
        return $patient->documents()
            ->orderByDesc('created_at')
            ->get();
    }

    public function addDocument(Patient $patient, array $data): PatientDocument
    {
        $file = $data['file'];
        $path = $file->store('patient-documents/' . $patient->id, 'public');

        return PatientDocument::create([
            'patient_id' => $patient->id,
            'institution_id' => $patient->institution_id,
            'document_type' => $data['document_type'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'uploaded_by' => auth()->id(),
        ]);
    }

    public function getAllergies(Patient $patient): \Illuminate\Database\Eloquent\Collection
    {
        return $patient->allergies()->get();
    }

    public function addAllergy(Patient $patient, array $data): PatientAllergy
    {
        return PatientAllergy::create([
            'patient_id' => $patient->id,
            'institution_id' => $patient->institution_id,
            'allergen' => $data['allergen'],
            'severity' => $data['severity'],
            'reaction' => $data['reaction'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function getInsurances(Patient $patient): \Illuminate\Database\Eloquent\Collection
    {
        return $patient->insurances()->get();
    }

    public function addInsurance(Patient $patient, array $data): PatientInsurance
    {
        return PatientInsurance::create([
            'patient_id' => $patient->id,
            'institution_id' => $patient->institution_id,
            'provider_name' => $data['provider_name'],
            'policy_number' => $data['policy_number'],
            'coverage_type' => $data['coverage_type'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'is_primary' => $data['is_primary'] ?? false,
        ]);
    }

    public function getContacts(Patient $patient): \Illuminate\Database\Eloquent\Collection
    {
        return $patient->contacts()->get();
    }

    public function addContact(Patient $patient, array $data): PatientContact
    {
        return PatientContact::create([
            'patient_id' => $patient->id,
            'institution_id' => $patient->institution_id,
            'name' => $data['name'],
            'relationship' => $data['relationship'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'is_emergency' => $data['is_emergency'] ?? false,
        ]);
    }

    public function getStats(Institution $institution): array
    {
        $patientQuery = Patient::byInstitution($institution->id);

        $totalPatients = $patientQuery->count();
        $activePatients = $patientQuery->where('is_active', true)->count();
        $newThisMonth = $patientQuery->where('created_at', '>=', now()->startOfMonth())->count();
        $genderDistribution = $patientQuery->selectRaw('gender, count(*) as count')
            ->groupBy('gender')
            ->pluck('count', 'gender')
            ->toArray();

        $ageDistribution = $patientQuery->selectRaw("
            CASE
                WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) < 18 THEN 'child'
                WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 18 AND 35 THEN 'young_adult'
                WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 36 AND 55 THEN 'middle_aged'
                ELSE 'senior'
            END as age_group,
            count(*) as count
        ")
            ->groupBy('age_group')
            ->pluck('count', 'age_group')
            ->toArray();

        return [
            'total_patients' => $totalPatients,
            'active_patients' => $activePatients,
            'new_this_month' => $newThisMonth,
            'gender_distribution' => $genderDistribution,
            'age_distribution' => $ageDistribution,
        ];
    }

    private function generateInternalCode(int $institutionId): string
    {
        $prefix = 'PT';
        $year = now()->format('Y');
        $lastPatient = Patient::where('institution_id', $institutionId)
            ->whereYear('created_at', now()->year)
            ->orderByDesc('id')
            ->value('internal_code');

        if ($lastPatient) {
            $lastNumber = (int) substr((string) $lastPatient, -6);
            $newNumber = str_pad((string) ($lastNumber + 1), 6, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '000001';
        }

        return "{$prefix}{$year}{$newNumber}";
    }

    private function generateMedicalRecordNumber(int $institutionId): string
    {
        $prefix = 'HC';
        $year = now()->format('Y');
        $lastRecord = Patient::where('institution_id', $institutionId)
            ->whereYear('created_at', now()->year)
            ->orderByDesc('id')
            ->value('medical_record_number');

        if ($lastRecord) {
            $lastNumber = (int) substr((string) $lastRecord, -6);
            $newNumber = str_pad((string) ($lastNumber + 1), 6, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '000001';
        }

        return "{$prefix}{$year}{$newNumber}";
    }
}
