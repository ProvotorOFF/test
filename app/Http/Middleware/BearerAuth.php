<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BearerAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->header("Authorization")) return response()->json([
            "status" => "error",
            "code" => 403,
            "message" => "Authorization is required"
        ], 403);
        if ($request->header("Authorization") != "Bearer " . ENV("TOKEN")) return response()->json([
            "status" => "error",
            "code" => 403,
            "message" => "Invalid token"
        ], 403);
        return $next($request);
    }
}
