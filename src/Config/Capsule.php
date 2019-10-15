<?php

namespace App\Database;

use \Illuminate\Database\Capsule\Manager as CapsuleManager;
use \Illuminate\Container\Container;

class Capsule extends CapsuleManager
{

	public function __construct() {
		parent::__construct();

		$settings = array (
			'driver'    => 'mysql',
			'host'      => 'localhost',
			'database'  => 'helf6u',
			'username'  => 'helf6u',
			'password'  => 'thebas',
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => ''
		);

		$this->addConnection($settings);
		$this->setAsGlobal();
		$this->bootEloquent();
	}

	public function ConnectionManager() {

	}
}
