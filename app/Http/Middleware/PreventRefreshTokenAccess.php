<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventRefreshTokenAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         if ($request->user() && $request->user()->tokenCan('refresh')) {
            return response()->json(['message' => 'Forbidden: refresh token cannot access this route.'], 403);
        }
        return $next($request);
    }
}
