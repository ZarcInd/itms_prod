<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogApiLatency
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Capture start time (in microseconds)
        $startTime = microtime(true);

        // Proceed with the request
        $response = $next($request);

        // Capture end time (in microseconds)
        $endTime = microtime(true);

        // Calculate latency in milliseconds
        $latency = ($endTime - $startTime) * 1000;

        // Log the latency as an error or info
        error_log("API Latency: " . round($latency, 2) . " ms for " . $request->method() . " " . $request->fullUrl());

        return $response;
    }
}
