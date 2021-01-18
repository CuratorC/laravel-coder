<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ObserverProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // 用户
        \App\Models\User::observe(\App\Observers\UserObserver::class);
        //<--观察者注册标记，请勿修改
    }
}
