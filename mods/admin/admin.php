<?php
/*
@package: Zipp
@version: 0.2 <2019-07-10>
*/

namespace Admin;

use Core\Module;
use Langs\Module as LangsModule;
use Router\Module as RouterModule;
use Admin\Module as AdminModule;
use Ajax\Module as AjaxModule;
use MagmaCSS\Engine;
use MagmaMinifier\Minifier;
use \Error;

class Admin extends Module {

	use LangsModule, RouterModule, AdminModule, AjaxModule;

	// contains all pages
	protected $pages = [];

	// contains all urls maping to pages
	protected $urls = [];

	// contains every page according to its section
	// section => [ title, pages ]
	protected $sections = [];

	// scripts
	protected $scripts = [];

	// styles
	protected $styles = [];

	// interactors
	protected $interactors = [];

	// GETTERS
	// gets the defined urls mapped to the pages
	public function _getUrls() {
		return $this->urls;
	}

	public function _getScripts() {

		$urls = [];
		$router = $this->mods->Router;

		if ( DEBUG ) {

			foreach ( $this->scripts as $sc )
				$urls[] = $router->url( 'mods/'. $sc[0]. '/'. $sc[1]. '.js' );
			return $urls;

		} else {

			$paths = [];
			foreach ( $this->scripts as $sc )
				$paths[] = $this->mods->get( $sc[0] )->path. str_replace( '/', DS, $sc[1] ). '.js';

			$minifier = new Minifier( TMP_PATH. 'adminjs'. DS );

			$newFile = $minifier->js( $paths, 'v1' );
			$urls[] = $router->url( 'tmp/adminjs/'. $newFile );

		}

		return $urls;

	}

	public function _getStyles() {
		
		$nUrls = [];
		$urls = [];
		$router = $this->mods->Router;

		$mgEngine = new Engine( TMP_PATH. 'adminmgcss'. DS, DEBUG );

		foreach ( $this->styles as $st ) {
			switch ( $st[2] ) {
				case 'mgcss':
					$path = str_replace( '/', DS, $st[1]. '.mgcss' );
					$path = $this->mods->get( $st[0] )->path. $path;
					$niceName = preg_replace( '/[^a-zA-Z0-9_-]/', '_', $st[0]. '_'. $st[1] );
					$nUrls[] = 'tmp/adminmgcss/'. $mgEngine->go( $path, $niceName );
					break;
				case 'css':
					$nUrls[] = 'mods/'. $st[0]. '/'. $st[1]. '.css';
					break;
			}
		}

		if ( DEBUG ) {

			foreach ( $nUrls as $url )
				$urls[] = $router->url( $url );

		} else {

			$paths = [];
			foreach ( $nUrls as $url )
				$paths[] = DIR. str_replace( '/', DS, $url );

			$minifier = new Minifier( TMP_PATH. 'admincss'. DS );

			$newFile = $minifier->css( $paths, 'v1' );
			$urls[] = $router->url( 'tmp/admincss/'. $newFile );

		}

		return $urls;

	}

	// METHODS
	public function addScript( string $mod, string $file ) {
		$this->scripts[] = [$mod, $file];
	}

	public function addScripts( string $mod, array $files ) {
		foreach ( $files as $file )
			$this->addScript( $mod, $file );
	}

	public function addStyle( string $mod, string $file, string $type = 'css' ) {
		$this->styles[] = [$mod, $file, $type];
	}

	public function register( string $intCls, string $mod ) {
		// $int->init( $this );
		$this->interactors[] = [$intCls, $mod];
	}

	public function initMainInteractor() {
		$int = new MainInteractor( $this );
		$int = $int->init( $this );
	}

	public function initInteractors() {

		$nInts = [];
		foreach ( $this->interactors as $int ) {
			$nInt = new $int[0]( $this->mods->get( $int[1] ) );
			$nInt->init( $this );
			$nInts[] = $nInt;
		}

		$this->interactors = $nInts;

	}

	public function isEnabled() {
		return $this->routerIsEnabled();
	}

	// pos === <0 left >0 right
	public function addSection( string $key, string $title, int $pos ) {
		$this->sections[$key] = [$pos, $title, []];
	}

	// add a page to the pages
	public function addPage( string $k, string $class, string $mod ) {
		$this->pages[$k] = [$mod, $class];
	}

	// add an url to an existing page
	public function addPageUrl( string $url, string $pageKey ) {

		$this->checkPage( $pageKey, 'url' );

		$l = len( $url );

		if ( !isset( $this->urls[$l] ) )
			$this->urls[$l] = [];

		$this->urls[$l][$url] = $pageKey;

	}

	// add an existing page to a section, this will automatically create the reqired url
	public function addPageToSection( string $section, string $pageKey, string $title, string $icon = null ) {

		$this->checkPage( $pageKey, 'section' );

		if ( !isset( $this->sections[$section] ) )
			throw new Error( sprintf( 'Could not asign page %s to section %s, section doenst exist', $pageKey, $section ) );

		$url = $section. '/';

		if ( $section !== $pageKey )
			$url .= $pageKey. '/';

		if ( isNil( $icon ) )
			$icon = 'default';

		$this->sections[$section][2][$pageKey] = [$title, $url, $icon];
		$this->addPageUrl( $url, $pageKey );

	}

	public function hasPage( string $key ) {
		return isset( $this->pages[$key] );
	}

	// returns an instance of the page requested
	public function getPage( string $key ) {

		$p = $this->pages[$key];

		if ( !is_array( $p ) )
			return $p;

		$this->pages[$key] = new $p[1]( $this, $p[0] );

		return $this->pages[$key];

	}

	public function getSections() {

		if ( !has( $this->sections ) )
			return null;

		$listL = [];
		$listR = [];

		foreach ( $this->sections as $k => $s ) {

			$items = [];

			foreach ( $s[2] as $pk => $p )
				$items[] = [$pk, $p[0], $this->bUrl( $p[1] ), $p[2] ];

			if ( $s[0] < 0 )
				$listL[] = [abs($s[0]), $k, $s[1], $items];
			else
				$listR[] = [$s[0], $k, $s[1], $items];

		}

		return [$listL, $listR];

	}

	public function bUrl( string $url = '' ) {
		return $this->mods->Router->url( 'admin/'. $url );
	}

	// check if a page exists
	protected function checkPage( string $key, string $type = '' ) {
		if ( !isset( $this->pages[$key] ) )
			throw new Error( sprintf( 'could not add page %s to %s', $key, $type ) );
	}

	// INIT
	public function onInit() {

		// setup lang
		$this->setupLang();

		$this->setupPages();

	}

	// determins what language should be chosen
	protected function setupLang() {

		$lang = $this->mods->Session->get( 'lang', null );

		if ( isNil( $lang ) )
			$lang = $this->getMatchingLang();

		$this->mods->Langs->setLang( $lang );

	}

	// get a matching language specific to the browser else return default
	protected function getMatchingLang() {

		$cfg = $this->mods->Configs->open( 'admin' );
		$supported = $cfg->supportedLangs;

		$def = 'en';

		if ( isNil( $supported ) || !has( $supported ) )
			return $def;

		// set default to the first supported lang if en is not a supported lang
		if ( !in_array( $def, $supported ) )
			$def = $supported[0];

		return $this->mods->Router->getMatchingLang( $supported, $def );

	}

	// setup the interactor for the router
	protected function setupPages() {

		if ( !$this->isEnabled() )
			return;

		$this->routerRegister( 'admin/', 'RouterInteractor' );
		$this->ajaxRegister( 'AjaxInteractor' );

	}

}