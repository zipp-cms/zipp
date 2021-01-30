<?php
/*
@package: Zipp
@version: 0.1 <2019-07-01>
*/

namespace Ajax;

use Core\Module;
use Router\Module as RouterModule;

class Ajax extends Module {

	use RouterModule;

	protected $enabled = false;

	protected $interactors = [];

	// GETTERS
	public function _getScriptFile() {
		return 'js/ajax';
	}

	// METHODS
	public function isEnabled() {
		return $this->enabled;
	}

	public function baseUrl( string $url = '' ) {
		return $this->mods->Router->url( 'ajax/'. $url );
	}

	public function script() {
		return sprintf( '<script src="%s.js"></script>', $this->url( $this->scriptFile ) );
	}

	public function register( string $intCls, string $mod ) {
		$this->interactors[$mod] = [$intCls, $mod];
	}

	public function getInt( string $k ) {

		if ( !isset( $this->interactors[$k] ) )
			return false;

		return new $this->interactors[$k][0]( $this->mods->get( $this->interactors[$k][1] ) );

	}

	// INIT
	public function onInit() {

		if ( !$this->routerIsEnabled() )
			return;

		$this->enabled = true;

		$this->routerRegister( 'ajax/', 'RouterInteractor' );

	}

}