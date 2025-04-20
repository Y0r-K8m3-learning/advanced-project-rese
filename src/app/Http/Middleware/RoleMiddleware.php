<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role_id): Response
    {

        if (!Auth::check()) {
            return redirect('/');
        }

        if (Auth::user()->role_id != $role_id) {
            abort(403, 'Unauthorized');
        }


        return $next($request);
    }
}
