<?php

namespace App\Providers;

use App\Models\PersonalAccessToken;
use Carbon\Carbon;
use HTMLPurifier_Config;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */

    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //  Carbon::setTestNow(Carbon::now()->addDay(4));
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

    }
}
