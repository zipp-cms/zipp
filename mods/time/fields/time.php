<?php
/*
@package: Zipp
@version: 0.1 <2019-06-01>
*/

namespace Time\Fields;

use \Error;

use Fields\Fields\Text;
use Time\Time as TimeModule;

class Time extends Text {

	public $type = 'time';

	public function out( object $input ) {
		$s = $this->getValue( $input );
		return $s ? TimeModule::toDate( $s ) : null;
	}

	public function exportValue( object $data ) {
		$s = $this->getValue( $data );
		return $s ? TimeModule::toIso( $s ) : null;
	}

}