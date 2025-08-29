<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class language
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $langHeader = $request->header("language", null);
        if($user) {
            $locle = $user->prefs()->first()->lang;
            if($locle !== 'auto') {
                App::setLocale($locle);
            } else if(in_array($langHeader, ['ar','en'])) {
                App::setLocale($langHeader);
            } else {
                App::setLocale('ar');
            }

            // $request->merge(['lang'=> ]);
        } else if (in_array($langHeader, ['ar','en'])) {
            App::setLocale($langHeader);
            // $request->merge(['lang'=> $langHeader]);
        }

        return $next($request);
    }
}
