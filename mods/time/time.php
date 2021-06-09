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
	public function _getStyleFile() {
		return ['css/style', 'mgcss'];
	}

	public function _getScriptFile() {
		return 'js/time';
	}

	// datetime to iso8601
	public static function toIso( string $utc ) {
		return Self::reformat( 'Y-m-d\TH:i:s\Z', $utc );
	}

	/// Reformats a datetime string to a given php date format string.
	public static function reformat( string $fmtStr, string $timeStr ) {
		$time = strtotime( $timeStr );
		return $time ? date( $fmtStr, $time ) : null;
	}

	// iso8601 to datetime
	public static function toDate( string $utc ) {
		return Self::reformat( 'Y-m-d H:i:s', $utc );
	}

	// INIT
	public function onInit() {
		$this->addField( 'Time' );
	}

}