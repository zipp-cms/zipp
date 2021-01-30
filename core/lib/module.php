<?php
/*
@package: Zipp
@version: 0.1 <2019-05-28>
*/

namespace Core;

class Module extends MagicGet {

	// VARIABLES
	protected $path = '';

	protected $slug = '';

	protected $namespace = '';

	protected $mods = null;

	// GETTERS
	public function _getPath() {
		return $this->path;
	}

	public function _getSlug() {
		return $this->slug;
	}

	public function _getMods() {
		return $this->mods;
	}

	public function _getNamespace() {
		return $this->namespace;
	}

	// METHODS
	public function cls( string $cls ) {
		return $this->namespace. '\\'. $cls;
	}

	// START
	public function __construct( string $path, string $slug, string $namespace, Modules $mods ) {

		$this->path = $path;
		$this->slug = $slug;
		$this->namespace = $namespace;
		$this->mods = $mods;

		if ( method_exists( $this, 'onConstruct' ) )
			$this->onConstruct();

	}

}