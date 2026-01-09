<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InternalApiKey
{
    /**
     * Handle an incoming request.
     *
     * Validates internal API key from header: X-Internal-Api-Key
     * Key is configured in .env as INTERNAL_API_KEY
     */
    public function handle(Request $request, Closure $next): Response
    {
        $providedKey = $request->header("X-Internal-Api-Key");
        $configuredKey = config("services.internal_api.key");

        if (empty($configuredKey)) {
            return response()->json([
                "ok" => false,
                "errors" => [
                    ["field" => "auth", "message" => "Internal API key not configured on server"]
                ]
            ], 500);
        }

        if (empty($providedKey)) {
            return response()->json([
                "ok" => false,
                "errors" => [
                    ["field" => "auth", "message" => "Missing X-Internal-Api-Key header"]
                ]
            ], 401);
        }

        if (!hash_equals($configuredKey, $providedKey)) {
            return response()->json([
                "ok" => false,
                "errors" => [
                    ["field" => "auth", "message" => "Invalid API key"]
                ]
            ], 401);
        }

        return $next($request);
    }
}
