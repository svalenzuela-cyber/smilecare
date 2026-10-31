<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Allow the request only if the logged-in user has one of the given roles.
     * Example usage in routes: ->middleware('role:admin') or ->middleware('role:admin,dentist')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // No logged-in user, or the user's role is not in the allowed list -> block
        if (! $request->user() || ! in_array($request->user()->role, $roles)) {
            abort(403, 'You do not have permission to access this page.');
        }

        // Role is allowed -> continue to the page
        return $next($request);
    }
}