<?php
/*
@package: Zipp
@version: 0.1 <2019-05-28>
*/

namespace Session;

use Core\Module;

class Session extends Module {

	protected $started = false;

	public function onInit() {

		$router = $this->mods->Router;

		if ( !$router->isEnabled() )
			return;

		$p = $router->basePath;
		$p = $p === '' || $p === '/' ? '/' : '/'. $p;
		session_set_cookie_params( 0, $p, $router->host, true, true );

		session_start();
		$this->started = true;

	}

	public function get( string $k, $def = null ) {

		if ( !$this->started )
			return $def;

		return $_SESSION[$k] ?? $def;

	}

	public function __get( string $k ) {
		return $this->get( $k );
	}

	public function set( string $k, string $v ) {

		if ( !$this->started )
			return false;

		$_SESSION[$k] = $v;
		return true;

	}

	public function __set( string $k, string $v ) {
		$this->set( $k, $v );
	}

	public function delete( string $k ) {
		unset( $_SESSION[$k] );
	}

	public function close() {

		if ( !$this->started )
			return;

		session_write_close();
		$this->started = false;

	}

	public function destroy() {

		if ( !$this->started )
			return;

		session_unset();
		session_destroy();
		session_write_close();

		// maybe should move this to cookies
		$router = $this->mods->Router;
		setcookie( session_name(), null, -1, $router->basePath, $router->host, true, true );

		$this->started = false;

	}

}