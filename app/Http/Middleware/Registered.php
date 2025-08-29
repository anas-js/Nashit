<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class Registered
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // $request->user()->registration();
        if($request->user()->isRegistered()) {
            return $next($request);
        } else {
           throw ValidationException::withMessages([
                "user" => "User Not registered"
            ])->status(401);
        }

    }
}
