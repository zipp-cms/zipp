<?php
/*
@package: Zipp
@version: 0.1 <2019-05-28>
*/

namespace Langs;

trait Module {

	public function _getLang() {
		return $this->mods->Langs->open( $this->path, $this->slug );
	}

}