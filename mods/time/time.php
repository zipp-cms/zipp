<?php
/*
@package: Zipp
@version: 0.1 <2019-05-28>
*/

namespace Time;

use Core\Module;
use CLI\Interactor;
use Router\Module as RouterModule;
use Fields\Module as FieldsModule;

class Time extends Module {

	use RouterModule, FieldsModule;

	// GETTERS
	public function _getScriptFile() {
		return 'js/time';
	}

	// datetime to iso8601
	public function toIso( string $utc ) {
		return str_replace( ' ', 'T', $utc ). 'Z';
	}

	// iso8601 to datetime
	public function toDate( string $utc ) {
		return substr( str_replace( 'T', ' ', $utc ), 0, len( $utc ) - 2 );
	}

	// create html time tag
	public function time( string $utc, string $format, string $lang ) {
		return sprintf( '<time datetime="%s" data-autoconvert data-lang="%s" data-format="%s">%s</time>', $this->toIso( $utc ), $lang, $format, $utc. ' UTC' );
	}

	public function script() {
		return sprintf( '<script src="%s.js"></script>', $this->url( $this->scriptFile ) );
	}

	// INIT
	public function onInit() {
		$this->addField( 'Time' );
	}

}