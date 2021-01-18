<?php

namespace CuratorC\Coder;

use Illuminate\Support\Facades\Route;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    // protected $defer = true; 惰性加载

    public function register()
    {
        $this->registerCreateCommand();
    }

    public function provides()
    {

    }

    /**
     * 在注册后启动服务
     *
     * @return void
     */
    public function boot()
    {
        // 指定视图目录
        $this->loadViewsFrom(__DIR__.'/views', 'coder');

        // 路由文件
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

    }

    private function registerCreateCommand()
    {
        $this->app->singleton('command.coder.create', function ($app) {
            return $app['CuratorC\Coder\Commands\CreateCommand'];
        });

        $this->commands('command.coder.create');
    }
}