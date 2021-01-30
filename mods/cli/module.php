<?php
/*
@package: Zipp
@version: 0.2 <2019-07-02>
*/

namespace CLI;

trait Module {

	public function cliRegister( string $prefix, string $desc, string $cls ) {
		$this->mods->CLI->register( $prefix, $desc, $this->cls( $cls ), $this->slug );
	}

	public function cliIsEnabled() {
		return $this->mods->CLI->isEnabled();
	}

}