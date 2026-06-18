<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\Report\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService,
    ) {
    }

    public function dashboard(Request $request): JsonResponse
    {
        try {
            $data = $this->reportService->getDashboard($request->user()->institution);

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Dashboard data retrieved successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve dashboard data.',
                'debug' => $e->getMessage(),
            ], 500);
        }
    }

    public function patients(Request $request): JsonResponse
    {
        try {
            $data = $this->reportService->getPatientReport(
                $request->user()->institution,
                $request->only(['date_from', 'date_to'])
            );

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Patient report generated successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to generate patient report.',
            ], 500);
        }
    }

    public function appointments(Request $request): JsonResponse
    {
        try {
            $data = $this->reportService->getAppointmentReport(
                $request->user()->institution,
                $request->only(['date_from', 'date_to', 'doctor_id'])
            );

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Appointment report generated successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to generate appointment report.',
            ], 500);
        }
    }

    public function revenue(Request $request): JsonResponse
    {
        try {
            $data = $this->reportService->getRevenueReport(
                $request->user()->institution,
                $request->only(['date_from', 'date_to'])
            );

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Revenue report generated successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to generate revenue report.',
            ], 500);
        }
    }

    public function lab(Request $request): JsonResponse
    {
        try {
            $data = $this->reportService->getLabReport(
                $request->user()->institution,
                $request->only(['date_from', 'date_to'])
            );

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Lab report generated successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to generate lab report.',
            ], 500);
        }
    }

    public function pharmacy(Request $request): JsonResponse
    {
        try {
            $data = $this->reportService->getPharmacyReport(
                $request->user()->institution,
                $request->only(['date_from', 'date_to'])
            );

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Pharmacy report generated successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to generate pharmacy report.',
            ], 500);
        }
    }

    public function hospitalization(Request $request): JsonResponse
    {
        try {
            $data = $this->reportService->getHospitalizationReport(
                $request->user()->institution,
                $request->only(['date_from', 'date_to'])
            );

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Hospitalization report generated successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to generate hospitalization report.',
            ], 500);
        }
    }

    public function diagnoses(Request $request): JsonResponse
    {
        try {
            $data = $this->reportService->getDiagnosisReport(
                $request->user()->institution,
                $request->only(['date_from', 'date_to'])
            );

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Diagnosis report generated successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to generate diagnosis report.',
                'debug' => $e->getMessage(),
            ], 500);
        }
    }

    public function export(Request $request): JsonResponse
    {
        $request->validate([
            'type' => ['required', 'string', 'in:excel,pdf'],
            'report' => ['required', 'string'],
            'data' => ['required', 'array'],
            'columns' => ['nullable', 'array'],
            'template' => ['nullable', 'string'],
        ]);

        try {
            if ($request->input('type') === 'excel') {
                return $this->reportService->exportExcel(
                    $request->input('data'),
                    $request->input('columns', [])
                );
            }

            return $this->reportService->exportPdf(
                $request->input('data'),
                $request->input('template', 'default')
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to export report.',
            ], 500);
        }
    }
}
