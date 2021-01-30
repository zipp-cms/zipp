<?php
/*
@package: Zipp
@version: 0.1 <2019-05-28>
*/

namespace Langs;

use Core\Module;

class Langs extends Module {

	// all Viewers
	protected $langs = [];

	protected $default = 'en';

	protected $lang = '';

	protected $possible = null;

	// set the basic language
	public function setLang( string $lang ) {
		$this->lang = $lang;
	}

	// open a cfg retuns a ConfigViewer
	public function open( string $path, string $mod ) {

		if ( !isset( $this->langs[$mod] ) )
			$this->langs[$mod] = new Viewer( $path. 'langs'. DS, $this->lang, $this->default );

		return $this->langs[$mod];

	}

	public function getAllPossible() {

		if ( isNil( $this->possible ) )
			$this->possible = ( include $this->path. 'allpossible.php' );

		return $this->possible;

	}

}