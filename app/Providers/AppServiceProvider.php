<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Setting;
use Config;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(\L5Swagger\L5SwaggerServiceProvider::class); 
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // $settings = Setting::latest()->first();
        // config()->set('settings', $settings);
        // Config::set([
        //     'app.name' => \config('settings.name'),
        //     'app.url' => \config('settings.url'),
        //     'app.env' => \config('settings.env'),
        //     'app.debug' => \config('settings.debug'),
        // ]);
    }
}
