<?php
/*
@package: Zipp
@version: 0.2 <2019-07-12>
*/

namespace LogsInt;

use Core\Module;
use Langs\Module as LangsModule;

class Logs extends Module {

	use LangsModule;

	// INIT
	public function onInit() {

		if ( $this->mods->has( 'Admin' ) )
			$this->admin = new SetupAdmin( $this );

		if ( $this->mods->has( 'Ajax' ) )
			$ajax = new SetupAjax( $this );

	}

}