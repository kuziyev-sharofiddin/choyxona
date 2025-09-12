<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $userRole = auth()->user()->role->name;

        if (!in_array($userRole, $roles)) {
            abort(403, 'Sizda bu sahifaga kirish huquqi yo\'q.');
        }

        return $next($request);
    }
}
