<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InstitutionAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.',
                'error' => 'unauthenticated',
            ], 401);
        }

        $targetInstitutionId = $this->resolveInstitutionId($request);

        if (!$targetInstitutionId) {
            return response()->json([
                'message' => 'Institution ID is required.',
                'error' => 'institution_required',
            ], 400);
        }

        $userInstitutionId = $user->institution_id ?? null;

        if (!$userInstitutionId) {
            return response()->json([
                'message' => 'You are not associated with any institution.',
                'error' => 'no_institution',
            ], 403);
        }

        if ((int) $userInstitutionId !== (int) $targetInstitutionId) {
            $hasAccess = $this->checkCrossInstitutionAccess($user, $targetInstitutionId);

            if (!$hasAccess) {
                return response()->json([
                    'message' => 'You do not have access to this institution\'s data.',
                    'error' => 'institution_access_denied',
                ], 403);
            }
        }

        $institution = DB::table('institutions')
            ->where('id', $targetInstitutionId)
            ->where('status', 'active')
            ->first();

        if (!$institution) {
            return response()->json([
                'message' => 'Institution not found or inactive.',
                'error' => 'institution_inactive',
            ], 404);
        }

        $request->attributes->set('verified_institution_id', (int) $targetInstitutionId);
        $request->attributes->set('verified_institution', $institution);

        return $next($request);
    }

    private function resolveInstitutionId(Request $request): ?int
    {
        $institutionId = $request->header('X-Institution-Id')
            ?? $request->input('institution_id')
            ?? $request->route('institutionId');

        if ($institutionId && is_numeric($institutionId)) {
            return (int) $institutionId;
        }

        return null;
    }

    private function checkCrossInstitutionAccess($user, int $targetInstitutionId): bool
    {
        $role = $user->role ?? null;

        if ($role === 'super_admin') {
            return true;
        }

        $hasAccess = DB::table('institution_user_access')
            ->where('user_id', $user->id)
            ->where('institution_id', $targetInstitutionId)
            ->where('is_active', true)
            ->exists();

        if ($hasAccess) {
            return true;
        }

        if (in_array($role, ['doctor', 'nurse', 'laboratory', 'radiologist'])) {
            $hasReferral = DB::table('referrals')
                ->where('referring_institution_id', $user->institution_id)
                ->where('receiving_institution_id', $targetInstitutionId)
                ->where('status', '!=', 'cancelled')
                ->exists();

            if ($hasReferral) {
                return true;
            }
        }

        return false;
    }
}
