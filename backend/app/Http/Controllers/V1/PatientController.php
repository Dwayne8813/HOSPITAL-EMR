<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Services\Patient\PatientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function __construct(
        private readonly PatientService $patientService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $result = $this->patientService->list($request);

            return response()->json([
                'success' => true,
                'data' => $result->items(),
                'message' => 'Patients retrieved successfully.',
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
                'message' => 'Failed to retrieve patients.',
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'document_type' => ['required', 'string', 'max:50'],
            'document_number' => ['required', 'string', 'max:50'],
            'birth_date' => ['required', 'date'],
            'gender' => ['required', 'string', 'in:M,F,O'],
            'blood_type' => ['nullable', 'string', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'marital_status' => ['nullable', 'string'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'city_id' => ['nullable', 'integer'],
            'department_id' => ['nullable', 'integer'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:50'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $patient = $this->patientService->create($request->all());

            return response()->json([
                'success' => true,
                'data' => $patient,
                'message' => 'Patient created successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to create patient.',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $patient = $this->patientService->getById($id);

            return response()->json([
                'success' => true,
                'data' => $patient,
                'message' => 'Patient retrieved successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Patient not found.',
            ], 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'document_type' => ['sometimes', 'string', 'max:50'],
            'document_number' => ['sometimes', 'string', 'max:50'],
            'birth_date' => ['sometimes', 'date'],
            'gender' => ['sometimes', 'string', 'in:M,F,O'],
            'blood_type' => ['nullable', 'string'],
            'marital_status' => ['nullable', 'string'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'city_id' => ['nullable', 'integer'],
            'department_id' => ['nullable', 'integer'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        try {
            $patient = Patient::findOrFail($id);
            $updated = $this->patientService->update($patient, $request->except(['id', 'created_at']));

            return response()->json([
                'success' => true,
                'data' => $updated,
                'message' => 'Patient updated successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Patient not found.',
            ], 404);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $patient = Patient::findOrFail($id);
            $this->patientService->softDelete($patient);

            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Patient deleted successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Patient not found.',
            ], 404);
        }
    }

    public function medicalHistory(int $id): JsonResponse
    {
        try {
            $patient = Patient::findOrFail($id);
            $history = $this->patientService->getMedicalHistory($patient);

            return response()->json([
                'success' => true,
                'data' => $history,
                'message' => 'Medical history retrieved successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Patient not found.',
            ], 404);
        }
    }

    public function documents(int $id): JsonResponse
    {
        try {
            $patient = Patient::findOrFail($id);
            $documents = $this->patientService->getDocuments($patient);

            return response()->json([
                'success' => true,
                'data' => $documents,
                'message' => 'Patient documents retrieved successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Patient not found.',
            ], 404);
        }
    }

    public function addDocument(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:10240'],
            'document_type' => ['required', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        try {
            $patient = Patient::findOrFail($id);
            $document = $this->patientService->addDocument($patient, $request->all());

            return response()->json([
                'success' => true,
                'data' => $document,
                'message' => 'Document uploaded successfully.',
            ], 201);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Patient not found.',
            ], 404);
        }
    }

    public function allergies(int $id): JsonResponse
    {
        try {
            $patient = Patient::findOrFail($id);
            $allergies = $this->patientService->getAllergies($patient);

            return response()->json([
                'success' => true,
                'data' => $allergies,
                'message' => 'Patient allergies retrieved successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Patient not found.',
            ], 404);
        }
    }

    public function addAllergy(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'allergen' => ['required', 'string', 'max:255'],
            'severity' => ['required', 'string', 'in:mild,moderate,severe'],
            'reaction' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $patient = Patient::findOrFail($id);
            $allergy = $this->patientService->addAllergy($patient, $request->all());

            return response()->json([
                'success' => true,
                'data' => $allergy,
                'message' => 'Allergy added successfully.',
            ], 201);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Patient not found.',
            ], 404);
        }
    }

    public function insurances(int $id): JsonResponse
    {
        try {
            $patient = Patient::findOrFail($id);
            $insurances = $this->patientService->getInsurances($patient);

            return response()->json([
                'success' => true,
                'data' => $insurances,
                'message' => 'Patient insurances retrieved successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Patient not found.',
            ], 404);
        }
    }

    public function addInsurance(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'provider_name' => ['required', 'string', 'max:255'],
            'policy_number' => ['required', 'string', 'max:100'],
            'coverage_type' => ['nullable', 'string', 'max:100'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_primary' => ['sometimes', 'boolean'],
        ]);

        try {
            $patient = Patient::findOrFail($id);
            $insurance = $this->patientService->addInsurance($patient, $request->all());

            return response()->json([
                'success' => true,
                'data' => $insurance,
                'message' => 'Insurance added successfully.',
            ], 201);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Patient not found.',
            ], 404);
        }
    }

    public function contacts(int $id): JsonResponse
    {
        try {
            $patient = Patient::findOrFail($id);
            $contacts = $this->patientService->getContacts($patient);

            return response()->json([
                'success' => true,
                'data' => $contacts,
                'message' => 'Patient contacts retrieved successfully.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Patient not found.',
            ], 404);
        }
    }

    public function addContact(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'relationship' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'is_emergency' => ['sometimes', 'boolean'],
        ]);

        try {
            $patient = Patient::findOrFail($id);
            $contact = $this->patientService->addContact($patient, $request->all());

            return response()->json([
                'success' => true,
                'data' => $contact,
                'message' => 'Contact added successfully.',
            ], 201);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Patient not found.',
            ], 404);
        }
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'term' => ['required', 'string', 'min:2'],
        ]);

        try {
            $results = $this->patientService->search($request->input('term'));

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
