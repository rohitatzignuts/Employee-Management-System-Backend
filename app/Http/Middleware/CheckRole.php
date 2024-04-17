<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        /**
         * check for user authentication
         */
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        /**
         * check for user authorization
         */
        if (count($roles) > 0 && !in_array(auth()->user()->role, $roles)) {
            return response()->json(['error' => 'you are not Unauthorized for this operation !!'], 403);
        }

        return $next($request);
    }
}
