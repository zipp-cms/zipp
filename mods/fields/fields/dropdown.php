<?php
/*
@package: Zipp
@version: 0.1 <2019-06-01>
*/

namespace Fields\Fields;

use Fields\Field;
use \Error;

class DropDown extends Field {

	public $type = 'dropdown';

	public $default = '';

	public $options = [];

	public function validate( object $input ) {

		$d = $this->getValue( $input );

		if ( !is_string( $d ) )
			return false;

		return isset( $this->options[$d] );

	}

	public function view( object $data ) {
		return $this->getValue( $data, null );
	}

	// PROTECTED
	protected function parseCfg( object $cfg ) {
		$this->options = (array) ($cfg->options ?? []);
	}

	protected function exportData( object $data ) {
		$opts = [];
		foreach ( $this->options as $k => $v )
			$opts[] = [$k, $v];
		return [ $opts ];
	}

}