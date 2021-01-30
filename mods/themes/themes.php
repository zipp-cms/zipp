<?php
/*
@package: Zipp
@version: 0.2 <2019-07-01>
*/

namespace Themes;

use Core\Module;
use Core\Autoloader;
use Pages\Page;
use Router\Request;
use \Error;

// other should register a route
// others should 
class Themes extends Module {

	protected $themesPath = '';

	protected $bTheme = null;

	// GETTERS
	public function _getThemesPath() {
		return $this->themesPath;
	}

	public function _getPageCats() {
		return $this->theme->pageCats;
	}

	public function _getSettings() {
		return $this->theme->settings;
	}

	public function _getLayoutNames() {

		$t = $this->theme;
		$layout = $t->layouts ?? [];

		$d = [];
		foreach ( $layout as $k => $l )
			$d[$k] = $l->name;

		return $d;

	}

	public function _getTheme() {

		if ( !isNil( $this->bTheme ) )
			return $this->bTheme;

		$class = $this->mods->Site->get( 'theme' );
		if ( isNil( $class ) )
			throw new Error( 'Please configure a theme' );
		
		$slug = lower( stripNs( $class ) );

		$this->bTheme = new $class( $slug, $this );

		return $this->bTheme;

	}

	// METHODS
	public function displayError( string $lang, Request $req ) {
		$this->theme->error404( $lang, $req );
	}

	public function displayPage( Page $page, Request $req ) {
		$this->theme->displayPage( $page, $req );
	}

	// returns the page layout
	public function resolvePage( Page $page ) {
		return $this->theme->resolvePage( $page );
	}

	public function getFieldsByLayout( string $lout ) {

		$t = $this->theme;

		$layout = $t->layouts[$lout] ?? null;

		if ( isNil( $layout ) )
			throw new Error( sprintf( 'layout "%s" could not be found', $lout ) );

		$comps = [];

		foreach ( $layout->components as $c ) {

			$co = $t->components[$c] ?? null;

			if ( isNil( $co ) )
				throw new Error( sprintf( 'could not find component "%s" in layout "%s"', $c, $lout ) );

			$comps[] = (object) [
				'name' => $co->name,
				'desc' => $co->desc,
				'fields' => $co->fields
			];

		}

		// layout fields will be at the end
		// only show this if there are fields
		if ( has( $layout->fields ) )
			$comps[] = (object) [
				'name' => $layout->name,
				'desc' => null, // layout->desc
				'fields' => $layout->fields
			];

		return $comps;

	}

	public function contentChanged( string $key, $data = null ) {
		$this->theme->contentChanged( $key, $data );
	}

	// INIT
	protected function onConstruct() {
		$this->themesPath = USER_PATH. 'themes'. DS;
		Autoloader::addPath( $this->themesPath );
	}

}