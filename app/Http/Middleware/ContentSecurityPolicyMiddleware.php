<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    protected function generateCspHeader()
    {
        // Customize the Content Security Policy based on your application's needs
        $csp = " frame-ancestors 'none';";
        $csp = " object-src 'none';";
        $csp .= " script-src 'self' localhost:5173 127.0.0.1:5173;";
        $csp .= " style-src 'self'  fonts.googleapis.com fonts.gstatic.com";
        // Add additional directives as needed
        // Example:
        // $csp .= " script-src 'self' https://cdn.example.com;";

        return $csp;
    }
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Set Content Security Policy headers
        if(!str_contains($request->url(), 'download-tender-files')){
            // dd(str_contains($request->url(), 'demand/download/pdf/attached'));
            $response->header('Content-Security-Policy', $this->generateCspHeader());
        }
        // $response->headers->set('X-Frame-Options', 'DENY');
        // $response->headers->set('X-Frame-Options', 'DENY');
        // $response->headers->set('X-XSS-Protection', '1; mode=block');
        // $response->headers->set('X-Permitted-Cross-Domain-Policies', 'master-only');
        // $response->headers->set('X-Content-Type-Options', 'nosniff');
        // $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');
        // $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        // $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, post-check=0, pre-check=0');
        // $response->headers->set('Pragma', 'no-cache');

        return $response;
    }
}
