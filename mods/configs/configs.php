<?php
/*
@package: Zipp
@version: 0.2 <2019-07-01>
*/

namespace Configs;

use Core\Module;

class Configs extends Module {

	// all ConfigViewers
	protected $cfgs = [];

	protected $cfgPath = '';

	protected function onConstruct() {
		$this->cfgPath = USER_PATH. 'configs'. DS;
	}

	// open a cfg retuns a ConfigViewer
	public function open( string $file ) {

		if ( !isset( $this->cfgs[$file] ) )
			$this->cfgs[$file] = new Viewer( $this->cfgPath. $file. '.mgcfg' );

		return $this->cfgs[$file];

	}

}