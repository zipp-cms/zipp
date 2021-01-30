<?php
/*
@package: Zipp
@version: 0.2 <2019-07-02>
*/

namespace Ajax;

trait Module {

	public function ajaxIsEnabled() {
		return $this->mods->Ajax->isEnabled();
	}

	public function ajaxRegister( string $cls ) {
		return $this->mods->Ajax->register( $this->cls( $cls ), $this->slug );
	}

}