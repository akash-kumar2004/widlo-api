<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->header('Accept') !== 'application/json') {
            return response()->json([
                'status' => 406,
                'message' => 'Not Acceptable. Please set Accept: application/json in header'
            ], 406);
        }

        $key = $request->header('X-Authorization');

        if ($key !== env('API_KEY')) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthorized. Invalid API Key'
            ], 401);
        }

        return $next($request);
    }
}
