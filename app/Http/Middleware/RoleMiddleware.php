<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role The role to check for.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            // Not logged in, redirect to login
            return redirect('login');
        }

        $user = Auth::user();

        if (!$user->hasRole($role)) {
            // Role not matched, abort or redirect
            // You can redirect to a specific 'unauthorized' page or just abort
            // abort(403, 'Unauthorized action.');

            // Or redirect to their own dashboard or a generic one
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')->with('error', 'You are not authorized to access this page.');
            } elseif ($user->isStudent()) {
                return redirect()->route('student.dashboard')->with('error', 'You are not authorized to access this page.');
            }
            // Fallback if role is weird or dashboard routes are not set for that role
            return redirect('/dashboard')->with('error', 'You are not authorized to access this page.');
        }

        return $next($request);
    }
}
