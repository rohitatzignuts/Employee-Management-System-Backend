<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class IsAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ... $roles): Response
    {
        if (!is_array($roles)) {
            $roles = [$roles];
        }

        if (!in_array(auth()->user()->role, $roles)) {
            $errorMessage = 'Unauthorized';
            return response()->json(['error' => $errorMessage], 403);
        }

        return $next($request);
    }
}

