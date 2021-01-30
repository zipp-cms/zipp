<?php
/*
@package: Zipp
@version: 0.2 <2019-08-23>
*/

namespace LogsInt;

use Core\ShadowModule;
use Ajax\Module as AjaxModule;

class SetupAjax extends ShadowModule {

	use AjaxModule;

	// INIT
	public function onConstruct() {

		if ( $this->ajaxIsEnabled() )
			$this->ajaxRegister( 'AjaxInteractor' );

	}

}