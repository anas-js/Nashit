<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class Profile
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         $profile = User::where("username", $request->route("username"))->get()->first();
         $request->route()->forgetParameter("username");

        if(!$profile) {
            // $request->merge([profile]);
            // return $next($request);
            throw ValidationException::withMessages([
                "user" => "User Not found"
            ]);
        } else if (!$profile->isRegistered()) {
           throw ValidationException::withMessages([
                "user" => "User Not found"
            ]);
        }

        $request->merge(["profile"=> $profile]);




      return $next($request);
    }
}
