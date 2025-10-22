<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Check if user has any of the required roles
        foreach ($roles as $role) {
            if ($role === 'manager' && $user->isManager()) {
                return $next($request);
            }
            if ($role === 'staff' && $user->isStaff()) {
                return $next($request);
            }
        }

        // If no roles match, redirect to dashboard
        return redirect()->route('dashboard')->with('error', 'Access denied. Insufficient privileges.');
    }
}