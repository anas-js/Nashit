<?php

namespace App\Http\Middleware;

use App\Models\Session as ModelsSession;
use Closure;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;

class SecuritySessions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $user = Auth::guard('sanctum')->user();

        if ($user) {
            $sessionDB = $user->session($request->session()->getId());
            if ($sessionDB->user_agent !== $request->userAgent()) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                throw ValidationException::withMessages(['Unauthenticated']);
            }
        }


        return $next($request);
    }
}
