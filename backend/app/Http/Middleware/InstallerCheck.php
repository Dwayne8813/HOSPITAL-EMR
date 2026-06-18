<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class InstallerCheck
{
    /**
     * Handle an incoming request.
     *
     * Checks if the system is already installed and redirects away from installer
     * if installation is complete.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if installer is already completed
        $isInstalled = config('app.installer_completed', false) ||
                       File::exists(storage_path('app/installed.txt'));

        if ($isInstalled) {
            // Redirect to home or login page
            return redirect('/');
        }

        return $next($request);
    }
}
