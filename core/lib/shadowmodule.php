<?php
/*
@package: Zipp
@version: 0.2 <2019-07-02>
*/

namespace Core;

class ShadowModule extends MagicGet {

	// VARIABLES
	protected $mod = null;

	// GETTERS
	public function _getPath() {
		return $this->mod->path;
	}

	public function _getSlug() {
		return $this->mod->slug;
	}

	public function _getMods() {
		return $this->mod->mods;
	}

	public function _getNamespace() {
		return $this->mod->namespace;
	}

	// METHODS
	public function cls( string $cls ) {
		return $this->mod->cls( $cls );
	}

	// START
	public function __construct( Module $mod ) {

		$this->mod = $mod;

		if ( method_exists( $this, 'onConstruct' ) )
			$this->onConstruct();

	}

}