<?php
/*
@package: Zipp
@version: 0.1 <2019-05-28>
*/

namespace Admin;

use Core\Module;
use Core\MagicGet;

class Interactor extends MagicGet {

	protected $mod = null;

	protected $mods = null;

	protected $admin = null;

	// GETTERS

	// METHODS
	public function init( Admin $admin ) {
		$this->admin = $admin;
		$this->onInit();
	}

	public function _getLang() {
		return $this->mod->lang;
	}

	public function _getPath() {
		return $this->mod->path. 'pages'. DS;
	}

	public function _getUsers() {
		return $this->mods->Users;
	}

	// INIT
	public function __construct( Module $mod ) {

		$this->mod = $mod;
		$this->mods = $mod->mods;

	}

	// PROTECTED
	protected function onInit() {}

	// pos === <0 left >0 right
	protected function addSection( string $slug, string $name, int $pos ) {
		$this->admin->addSection( $slug, $name, $pos );
	}

	protected function addPage( string $k, string $cls = null ) {
		if ( isNil( $cls ) ) {
			$cls = $k;
			$k = lower( stripNs( $cls ) );
		}
		$this->admin->addPage( $k, $this->mod->cls( $cls ), $this->mod->slug );
		return $k;
	}

	protected function addPageUrl( string $url, string $pageKey ) {
		$this->admin->addPageUrl( $url, $pageKey );
	}

	protected function addPageToSection( string $section, string $pageKey, string $name, string $icon = null ) {
		$this->admin->addPageToSection( $section, $pageKey, $name, $icon );
	}

	protected function addSingle( string $cls, string $section, string $title, string $icon = null ) {
		$k = $this->addPage( $cls );
		$this->addPageToSection( $section, $k, $title, $icon );
	}

	protected function addMultiple( array $ar ) {

		foreach ( $ar as $a )
			$this->addSingle( $a[0], $a[1], $a[2], $a[3] ?? 'default' );

	}

	protected function isLoggedIn() {
		return $this->users->isLoggedIn();
	}

	protected function isAdmin() {
		return $this->users->isAdmin();
	}

	public function modUrl( string $url = '' ) {
		return $this->mods->Router->url( 'mods/'. $this->mod->slug. '/'. $url );
	}

	// url string or array
	protected function addStyle( string $url, string $type = 'css' ) {
		$this->admin->addStyle( $this->mod->slug, 'css/'. $url, $type );
	}

	protected function addStyles( array $urls, string $type = 'css' ) {
		foreach ( $urls as $u )
			$this->addStyle( $u, $type );
	}

	// url string or array
	protected function addScript( string $url ) {
		$this->admin->addScript( $this->mod->slug, 'js/'. $url );
	}

	protected function addScripts( array $urls ) {
		foreach ( $urls as $u )
			$this->addScript( $u );
	}

}