<?php
// app/Http/Middleware/RoleMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Uses the session's 'view_as' key instead of the raw DB role,
     * so admins switching to employee view are properly restricted.
     *
     * Real admins set view_as = 'admin' or 'employee'.
     * Real employees only ever have view_as = 'employee'.
     */
    public function handle(Request $request, Closure $next, string $role): mixed
    {
        // The effective role is controlled by 'view_as' (set in AuthController)
        $viewAs = session('view_as');

        // If no session yet, fall back to the DB role
        if (!$viewAs) {
            $viewAs = auth()->user()?->employee?->access?->user_access ?? 'employee';
        }

        if ($viewAs !== $role) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}