<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Certificate::where('institution_id', auth()->user()->institution_id)
                ->with(['patient', 'doctor']);

            if ($request->has('patient_id')) {
                $query->where('patient_id', $request->input('patient_id'));
            }

            if ($request->has('doctor_id')) {
                $query->where('doctor_id', $request->input('doctor_id'));
            }

            if ($request->has('type')) {
                $query->where('type', $request->input('type'));
            }

            $certificates = $query->orderByDesc('created_at')
                ->paginate($request->input('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $certificates->items(),
                'message' => 'Certificates retrieved successfully.',
                'meta' => [
                    'current_page' => $certificates->currentPage(),
                    'last_page' => $certificates->lastPage(),
                    'per_page' => $certificates->perPage(),
                    'total' => $certificates->total(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve certificates.',
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'doctor_id' => ['required', 'integer', 'exists:users,id'],
            'medical_record_id' => ['nullable', 'integer', 'exists:medical_records,id'],
            'type' => ['required', 'string', 'in:sick_leave,disability,fitness,death,other'],
            'content' => ['required', 'string'],
            'issue_date' => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:issue_date'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $certificate = Certificate::create([
                'institution_id' => auth()->user()->institution_id,
                ...$request->all(),
                'certificate_number' => $this->generateCertificateNumber(),
            ]);

            return response()->json([
                'success' => true,
                'data' => $certificate->load(['patient', 'doctor']),
                'message' => 'Certificate created successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to create certificate.',
            ], 500);
        }
    }

    public function pdf(int $id): JsonResponse
    {
        try {
            $certificate = Certificate::with(['patient', 'doctor', 'institution'])->findOrFail($id);

            $pdf = \Barryvdh\DomPDF\Facades\Pdf::loadView('certificates.pdf', [
                'certificate' => $certificate,
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'pdf_url' => 'data:application/pdf;base64,' . base64_encode($pdf->output()),
                ],
                'message' => 'Certificate PDF generated.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Certificate not found.',
            ], 404);
        }
    }

    private function generateCertificateNumber(): string
    {
        $prefix = 'CERT';
        $year = now()->format('Y');
        $lastCert = Certificate::where('institution_id', auth()->user()->institution_id)
            ->whereYear('created_at', now()->year)
            ->orderByDesc('id')
            ->value('certificate_number');

        if ($lastCert) {
            $lastNumber = (int) substr($lastCert, -6);
            $newNumber = str_pad((string) ($lastNumber + 1), 6, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '000001';
        }

        return "{$prefix}{$year}{$newNumber}";
    }
}
