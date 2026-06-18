<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuditLog
{
    private array $methodsToLog = ['POST', 'PUT', 'PATCH', 'DELETE'];

    private array $uriExclusions = [
        'api/health',
        'api/auth/login',
        'api/auth/logout',
        'sanctum/csrf-cookie',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldLog($request)) {
            $this->logAction($request, $response);
        }

        return $response;
    }

    private function shouldLog(Request $request): bool
    {
        if (!in_array($request->method(), $this->methodsToLog)) {
            return false;
        }

        $path = $request->path();
        foreach ($this->uriExclusions as $exclusion) {
            if (Str::startsWith($path, $exclusion)) {
                return false;
            }
        }

        return true;
    }

    private function logAction(Request $request, Response $response): void
    {
        $user = Auth::user();
        $method = $request->method();
        $action = $this->determineAction($method, $request);

        $oldValues = null;
        $newValues = null;

        if (in_array($method, ['PUT', 'PATCH']) && $request->route()?->parameter('id')) {
            $model = $this->resolveModel($request);
            if ($model) {
                $oldValues = collect($model->toArray())
                    ->only($request->except(['_token', '_method']))
                    ->map(fn($value) => is_null($value) ? 'null' : $value)
                    ->toArray();
                $newValues = $request->except(['_token', '_method']);
            }
        }

        if ($method === 'DELETE') {
            $model = $this->resolveModel($request);
            if ($model) {
                $oldValues = $model->toArray();
            }
        }

        if ($method === 'POST' && $response->getStatusCode() < 400) {
            $newValues = $request->except(['_token', '_method', 'password', 'password_confirmation']);
        }

        try {
            DB::table('audit_logs')->insert([
                'institution_id' => $request->input('institution_id'),
                'user_id' => $user?->id,
                'user_name' => $user?->name ?? 'System',
                'action' => $action,
                'auditable_type' => $this->resolveModelClass($request),
                'auditable_id' => $request->route()?->parameter('id'),
                'old_values' => $oldValues ? json_encode($oldValues) : null,
                'new_values' => $newValues ? json_encode($newValues) : null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => $method,
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to create audit log', [
                'error' => $e->getMessage(),
                'action' => $action,
                'url' => $request->fullUrl(),
            ]);
        }
    }

    private function determineAction(string $method, Request $request): string
    {
        return match ($method) {
            'POST' => 'created',
            'PUT', 'PATCH' => 'updated',
            'DELETE' => 'deleted',
            default => 'unknown',
        };
    }

    private function resolveModelClass(Request $request): ?string
    {
        $route = $request->route();
        if (!$route) {
            return null;
        }

        $action = $route->getAction();
        $controller = $action['controller'] ?? null;

        if (!$controller) {
            return null;
        }

        $class = class_basename($controller);
        $modelMap = [
            'PatientController' => 'App\\Models\\Patient',
            'MedicalRecordController' => 'App\\Models\\MedicalRecord',
            'AppointmentController' => 'App\\Models\\Appointment',
            'PrescriptionController' => 'App\\Models\\Prescription',
            'InvoiceController' => 'App\\Models\\Invoice',
            'PaymentController' => 'App\\Models\\Payment',
            'MedicationController' => 'App\\Models\\Medication',
            'InventoryItemController' => 'App\\Models\\InventoryItem',
            'UserController' => 'App\\Models\\User',
        ];

        return $modelMap[$class] ?? null;
    }

    private function resolveModel(Request $request): ?\Illuminate\Database\Eloquent\Model
    {
        $modelClass = $this->resolveModelClass($request);
        if (!$modelClass || !class_exists($modelClass)) {
            return null;
        }

        $id = $request->route()?->parameter('id');
        if (!$id) {
            return null;
        }

        try {
            return $modelClass::withoutGlobalScopes()->find($id);
        } catch (\Exception $e) {
            return null;
        }
    }
}
