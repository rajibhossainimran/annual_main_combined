<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionsPolicyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Set Permissions-Policy header
        $response->headers->set('Permissions-Policy', $this->generatePermissionsPolicyHeader());

        return $response;
    }

    protected function generatePermissionsPolicyHeader()
    {
        // Customize the Permissions-Policy based on your application's needs
        return "geolocation=(self '".env('APP_URL')."'), microphone=(), camera=(), fullscreen=()";
    }
}
