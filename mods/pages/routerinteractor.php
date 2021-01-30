<?php
/*
@package: Zipp
@version: 0.1 <2019-05-28>
*/

namespace Pages;

use Router\Interactor;
use Router\Request;
use \Error;

class RouterInteractor extends Interactor {

	public function on( Request $req ) {
		
		$langs = $this->mods->Site->get( 'languages', [] );
		if ( !has( $langs ) )
			throw new Error( 'please configure site languages' );

		$lang = $langs[0];
		$uri = $req->uri;

		if ( $this->mod->isMl() ) {

			$lang = $this->parseUriLang( $uri, $langs );

			// if lang is null
			// we need to detect which language to use
			// and then redirect the user (or )
			if ( isNil( $lang ) ) {
			
				$lang = $this->mods->Router->getMatchingLang( $langs, $langs[0] );

				$req->redirect( sprintf( '%s/%s', $lang, $uri === '/' ? '' : $uri ), true );
				die;

			}

			$uri = rtrim( substr( $uri, len( $lang ) + 1 ), '/' ). '/';

		}

		// get page by url and lang
		$t = $this->mods->Themes;
		$p = $this->mod->getByUrl( $uri, $lang );

		// TODO: check if it is needed
		/*if ( $this->mods->has( 'Logs' ) && !defined( 'PAGES_SHOW_PREVIEW' ) ) {
			$this->mods->Logs->log( 'req', $req->uri );
		}*/
		
		if ( !$p )
			return $t->displayError( $lang, $req );

		$this->mod->setActivePage( $p );

		$t->displayPage( $p, $req );

	}

	protected function parseUriLang( string $uri, array $langs ) {

		if ( !cLen( $uri, 3 ) ) // need to count / to
			return null;

		// 5er snipped
		if ( cLen( $uri, 6 ) ) {

			$s = substr( $uri, 0, 6 );

			foreach ( $langs as $lng )
				if ( $lng. '/' === $s )
					return $lng;

		}

		$s = substr( $uri, 0, 3 );

		foreach ( $langs as $lng )
			if ( $lng. '/' === $s )
				return $lng;

		return null;

	}

}