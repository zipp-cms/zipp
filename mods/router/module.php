<?php
/*
@package: Zipp
@version: 0.1 <2019-05-28>
*/

namespace Router;

trait Module {

	// is a function for the router
	public function url( string $url = '' ) {
		return $this->mods->Router->url( 'mods/'. $this->slug. '/'. $url );
	}

	public function routerIsEnabled() {
		return $this->mods->Router->isEnabled();
	}

	public function routerRegister( string $url, string $cls ) {
		$this->mods->Router->register( $url, $this->cls( $cls ), $this->slug );
	}

}