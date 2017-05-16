<?php
/**
 * Created by PhpStorm.
 * User: csi0n
 * Date: 5/14/17
 * Time: 5:39 PM
 */

namespace csi0n\Laravel\Datatables\Providers;


use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application as LaravelApplication;

class CLaravelDatatablesServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->setupConfig();
    }

    protected function setupConfig()
    {
        $config = __DIR__ . '/../Config/datatables.php';
        if ($this->app instanceof LaravelApplication) {
            if ($this->app->runningInConsole()) {
                $this->publishes([
                    $config => config_path('datatables.php')
                ], 'datatables');
            }
        }
        $this->mergeConfigFrom($config, 'datatables');
    }

    public function register()
    {
        $this->app->singleton('csi0n.laravel.datatables', function () {
            if (config('datatables.drive') == 'datatables')
                return new \csi0n\Laravel\Datatables\Repositories\CLaravelDatatablesRepository();
            else if (config('datatables.drive') == 'element')
                return new \csi0n\Laravel\Datatables\Repositories\ElementTableRepository();
        });
    }
}