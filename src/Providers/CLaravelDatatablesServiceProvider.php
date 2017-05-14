<?php
/**
 * Created by PhpStorm.
 * User: csi0n
 * Date: 5/14/17
 * Time: 5:39 PM
 */

namespace csi0n\Laravel\Datatables\Providers;


use Illuminate\Support\ServiceProvider;

class CLaravelDatatablesServiceProvider extends ServiceProvider {

	public function boot() {

	}

	public function register() {
		$this->app->singleton( 'csi0n.laravel.datatables', function () {
			return new \csi0n\Laravel\Datatables\Repositories\CLaravelDatatablesRepository();
		} );
	}
}