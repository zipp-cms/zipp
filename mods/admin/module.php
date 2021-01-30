<?php
/*
@package: Zipp
@version: 0.2 <2019-07-02>
*/

namespace Admin;

trait Module {

	public function adminRegister( string $cls ) {
		return $this->mods->Admin->register( $this->cls( $cls ), $this->slug );
	}

	public function adminIsEnabled() {
		return $this->mods->Admin->isEnabled();
	}

}