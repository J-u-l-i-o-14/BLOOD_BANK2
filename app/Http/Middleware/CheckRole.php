<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Si l'utilisateur est un admin, il a accès à tout
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Convertir les rôles en tableau et traiter les rôles multiples
        $allowedRoles = collect($roles)->flatMap(function ($role) {
            return explode(',', $role);
        })->toArray();

        // Vérifier si l'utilisateur a l'un des rôles requis
        if (in_array($user->role, $allowedRoles)) {
            return $next($request);
        }

        // Si l'utilisateur n'a pas les droits requis
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        return redirect()
            ->back()
            ->with('error', 'Vous n\'avez pas les permissions nécessaires pour accéder à cette ressource.');
    }
}
