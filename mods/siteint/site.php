<?php
/*
@package: Zipp
@version: 0.1 <2019-06-03>
*/

namespace SiteInt;

use Core\Module;
use Langs\Module as LangsModule;
// use Router\Request;
use \Error;

class Site extends Module {

	use LangsModule;

	// GETTERS
	public function _getLangs() {
		return (array) $this->mods->Site->get( 'languages' , [] );
	}

	public function _getMultilingual() {
		return (bool) $this->mods->Site->get( 'multilingual', false );
	}

	// get langs with nice Title
	public function _getNiceLangs() {

		$lOpts = $this->mods->Langs->getAllPossible();

		$langs = $this->langs;

		$d = [];

		foreach ( $langs as $l )
			$d[$l] = $lOpts[$l] ?? 'unknown';

		return $d;

	}

	// METHODS
	// returns always a lang, but doenst garantie that the lang is correct
	public function getRawCookieLang() {
		return $_COOKIE['ctn-lang'] ?? $this->getDefaultLang();
	}

	// this function is "fault tolerant"
	public function getCookieLang() {
		try {
			return $this->getValidatedLang( $_COOKIE['ctn-lang'] ?? null );
		} catch ( Error $e ) {
			return $this->getDefaultLang();
		}
	}

	public function getValidatedLang( string $lang = null ) {

		$s = $this->mods->Site;
		$langs = $s->get( 'languages' );

		if ( isNil( $lang ) )
			return $this->getDefaultLang( $langs );

		if ( !in_array( $lang, $langs ) )
			throw new Error( 'Language not valid!' );

		return $lang;

	}

	// here the site must be configured
	public function getDefaultLang( array $langs = null ) {

		$langs = $langs ?? $this->mods->Site->get( 'languages' );
		$lang = $this->mods->Router->getMatchingLang( $langs, $langs[0] );

		return $lang;

	}

	// INIT
	public function onInit() {

		if ( $this->mods->has( 'CLI' ) )
			$this->cli = new SetupCLI( $this );

		if ( $this->mods->has( 'Admin' ) )
			$this->admin = new SetupAdmin( $this );

	}

}