<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnsureInstitution
{
    public function handle(Request $request, Closure $next): Response
    {
        $institutionId = $request->header('X-Institution-Id')
            ?? $request->query('institution_id')
            ?? $this->extractFromToken();

        if (!$institutionId) {
            return response()->json([
                'message' => 'Institution context is required.',
                'error' => 'institution_required',
            ], 400);
        }

        $institution = DB::table('institutions')
            ->where('id', $institutionId)
            ->where('status', 'active')
            ->first();

        if (!$institution) {
            return response()->json([
                'message' => 'Institution not found or inactive.',
                'error' => 'institution_invalid',
            ], 404);
        }

        $request->merge(['institution_id' => (int) $institutionId]);
        $request->attributes->set('institution', $institution);

        return $next($request);
    }

    private function extractFromToken(): ?int
    {
        $user = Auth::user();

        if ($user && property_exists($user, 'institution_id')) {
            return $user->institution_id;
        }

        $token = request()->bearerToken();

        if ($token) {
            try {
                $payload = \Firebase\JWT\JWT::decode(
                    $token,
                    new \Firebase\JWT\Key(config('app.jwt_secret', config('app.key'))),
                    ['HS256']
                );

                return $payload->institution_id ?? null;
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }
}
