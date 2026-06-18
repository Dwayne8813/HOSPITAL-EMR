<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    private const ROLE_PERMISSIONS = [
        'super_admin' => ['*'],
        'institution_admin' => [
            'users.manage',
            'users.view',
            'patients.manage',
            'patients.view',
            'appointments.manage',
            'appointments.view',
            'medical_records.manage',
            'medical_records.view',
            'prescriptions.manage',
            'prescriptions.view',
            'lab_orders.manage',
            'lab_orders.view',
            'billing.manage',
            'billing.view',
            'inventory.manage',
            'inventory.view',
            'reports.view',
            'settings.manage',
            'rooms.manage',
            'rooms.view',
        ],
        'medical_director' => [
            'users.view',
            'patients.manage',
            'patients.view',
            'appointments.manage',
            'appointments.view',
            'medical_records.manage',
            'medical_records.view',
            'prescriptions.manage',
            'prescriptions.view',
            'lab_orders.manage',
            'lab_orders.view',
            'reports.view',
        ],
        'doctor' => [
            'patients.view',
            'patients.create',
            'patients.update',
            'appointments.view',
            'appointments.create',
            'appointments.update',
            'medical_records.manage',
            'medical_records.view',
            'prescriptions.manage',
            'prescriptions.view',
            'lab_orders.create',
            'lab_orders.view',
            'certificates.create',
            'referrals.create',
        ],
        'nurse' => [
            'patients.view',
            'patients.update',
            'appointments.view',
            'medical_records.view',
            'medical_records.update',
            'vitals.manage',
            'medications.dispense',
        ],
        'receptionist' => [
            'patients.create',
            'patients.view',
            'patients.update',
            'appointments.create',
            'appointments.view',
            'appointments.update',
            'appointments.cancel',
            'billing.view',
            'invoices.create',
            'invoices.view',
        ],
        'pharmacist' => [
            'prescriptions.view',
            'prescriptions.dispense',
            'medications.manage',
            'medications.view',
            'inventory.view',
            'inventory.dispense',
        ],
        'laboratory' => [
            'lab_orders.view',
            'lab_orders.update',
            'lab_orders.complete',
            'lab_results.manage',
        ],
        'radiologist' => [
            'imaging_orders.view',
            'imaging_orders.update',
            'imaging_orders.complete',
            'imaging_results.manage',
        ],
        'accountant' => [
            'billing.manage',
            'billing.view',
            'invoices.view',
            'invoices.manage',
            'payments.view',
            'payments.manage',
            'reports.financial',
        ],
        'patient' => [
            'profile.view',
            'profile.update',
            'appointments.view',
            'appointments.create',
            'medical_records.view',
            'prescriptions.view',
            'lab_results.view',
            'invoices.view',
        ],
    ];

    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.',
                'error' => 'unauthenticated',
            ], 401);
        }

        $userRole = $user->role ?? null;

        if (!$userRole) {
            return response()->json([
                'message' => 'No role assigned to your account.',
                'error' => 'no_role',
            ], 403);
        }

        if (!isset(self::ROLE_PERMISSIONS[$userRole])) {
            return response()->json([
                'message' => 'Invalid role assigned.',
                'error' => 'invalid_role',
            ], 403);
        }

        $userPermissions = self::ROLE_PERMISSIONS[$userRole];

        if (in_array('*', $userPermissions)) {
            return $next($request);
        }

        if (empty($permissions)) {
            return $next($request);
        }

        foreach ($permissions as $permission) {
            if ($this->userHasPermission($userPermissions, $permission)) {
                return $next($request);
            }
        }

        return response()->json([
            'message' => 'You do not have the required permissions to perform this action.',
            'error' => 'insufficient_permissions',
            'required' => $permissions,
        ], 403);
    }

    private function userHasPermission(array $userPermissions, string $requiredPermission): bool
    {
        if (in_array($requiredPermission, $userPermissions)) {
            return true;
        }

        $parts = explode('.', $requiredPermission);
        array_pop($parts);

        while (!empty($parts)) {
            $wildcard = implode('.', $parts) . '.*';
            if (in_array($wildcard, $userPermissions)) {
                return true;
            }
            array_pop($parts);
        }

        return false;
    }
}
