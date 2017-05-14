<?php

namespace csi0n\Laravel\Datatables\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Created by PhpStorm.
 * User: csi0n
 * Date: 5/14/17
 * Time: 5:37 PM
 */
class CLaravelDatatablesFacade extends Facade {
	protected static function getFacadeAccessor() {
		return 'csi0n.laravel.datatables';
	}
}