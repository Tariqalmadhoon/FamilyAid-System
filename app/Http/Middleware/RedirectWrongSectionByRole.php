<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectWrongSectionByRole
{
    /**
     * Redirect authenticated users to the correct section for their role
     * instead of showing a hard 403 when they open the wrong area manually.
     */
    public function handle(Request $request, Closure $next, string $section): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($section === 'citizen' && ! $user->hasRole('citizen')) {
            if ($user->hasAnyRole(['admin', 'data_entry', 'auditor', 'distributor', 'camp_manager'])) {
                return redirect()->route('admin.dashboard');
            }
        }

        if ($section === 'admin' && $user->hasRole('citizen')) {
            if ($user->household_id) {
                return redirect()->route('citizen.dashboard');
            }

            return redirect()->route('citizen.onboarding');
        }

        return $next($request);
    }
}
