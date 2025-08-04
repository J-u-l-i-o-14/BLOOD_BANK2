<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class FormSecurityMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifie le token CSRF
        if ($request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('DELETE')) {
            if (!$request->hasValidSignature() && !$this->hasValidCsrfToken($request)) {
                abort(419, 'La session a expiré. Veuillez réessayer.');
            }
        }

        // Rate limiting pour les formulaires
        if ($request->isMethod('POST')) {
            $key = 'form_submissions_' . $request->ip();
            $maxAttempts = 10; // Maximum de soumissions par minute
            $attempts = cache()->get($key, 0);

            if ($attempts >= $maxAttempts) {
                abort(429, 'Trop de tentatives. Veuillez réessayer dans une minute.');
            }

            cache()->put($key, $attempts + 1, 60);
        }

        // Ajoute des en-têtes de sécurité
        $response = $next($request);
        
        return $response->withHeaders([
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-XSS-Protection' => '1; mode=block',
            'X-Content-Type-Options' => 'nosniff',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline';"
        ]);
    }

    /**
     * Vérifie si le token CSRF est valide
     */
    protected function hasValidCsrfToken($request)
    {
        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');
        
        if (!$token && $header = $request->header('X-XSRF-TOKEN')) {
            $token = decrypt($header);
        }

        return $token && hash_equals(Session::token(), $token);
    }
}
