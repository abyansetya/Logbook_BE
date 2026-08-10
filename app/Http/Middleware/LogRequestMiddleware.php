<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogRequestMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        $response = $next($request);

        $duration = round((microtime(true) - $startTime) * 1000, 2); // ms

        $user = Auth::user();
        $userId = $user ? $user->id : 'Guest';
        $method = $request->method();
        $url = $request->path();
        $ip = $request->ip();
        $status = $response->getStatusCode();

        // Format: [REQUEST] METHOD URL | User: ID | IP: x.x.x.x | Duration: Xms | Status: SSS
        Log::info("[REQUEST] {$method} {$url} | User: {$userId} | IP: {$ip} | Duration: {$duration}ms | Status: {$status}");

        return $response;
    }
}
