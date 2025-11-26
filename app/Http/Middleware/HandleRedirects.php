<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleRedirects
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get current path (without query string)
        $currentPath = $request->path();

        // Normalize: ensure leading slash, remove trailing slash
        $normalizedPath = Redirect::normalizePath('/' . $currentPath);

        // Check if redirect exists for this path
        $redirect = Redirect::findByPath($normalizedPath);

        if ($redirect) {
            // Record hit for analytics
            $redirect->recordHit();

            // Return redirect response
            return redirect($redirect->new_path, $redirect->status_code);
        }

        // No redirect found, continue normal request
        return $next($request);
    }
}
