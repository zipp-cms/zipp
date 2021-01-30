<?php
/*
@package: Zipp
@version: 0.2 <2019-07-12>
*/

namespace LogsInt;

use Core\ShadowModule;
use Admin\Module as AdminModule;

class SetupAdmin extends ShadowModule {

	use AdminModule;

	// INIT
	public function onConstruct() {

		if ( $this->adminIsEnabled() )
			$this->adminRegister( 'AdminInteractor' );

	}

}