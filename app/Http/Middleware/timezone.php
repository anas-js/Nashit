<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class timezone
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $tz = $request->header("timezone");
        if ($user) {
            $request->merge(["tz" => $user->timezone]);
        } else if ($tz && in_array($tz,timezone_identifiers_list())) {
            $timezone = now($tz)->format("P");

            // if ($timezone < 0) {
            //     $timezone =  "$timezone:00";
            // } else {
            //     $timezone =  "+$timezone:00";
            // }
            $request->merge(["tz" => $timezone]);
        } else {
            $request->merge(["tz" => "+3:00"]);
        }
        return $next($request);
    }
}
