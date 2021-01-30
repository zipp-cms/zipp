<?php
/*
@package: Zipp
@version: 1.0 <2019-06-01>
*/

namespace Fields\Fields;

use Fields\Field;
use \Error;

class SingleRepeat extends Field {

	public $type = 'singlerepeat';

	public $default = '';

	protected $field = null;

	protected $addText = '';

	protected $removeText = '';

	// PROTECTED
	protected function parseCfg( object $cfg ) {

		$this->field = $cfg->field ?? null;
		if ( isNil( $this->field ) )
			throw new Error( sprintf( 'the singlerepeat field (%s) is missing a field to repeat', $this->slug ) );

		$this->name = $this->field->name;
		$this->desc = $this->field->desc;

		$this->addText = $cfg->addBtn ?? 'addBtn';
		$this->removeText = $cfg->removeBtn ?? 'removeBtn';

	}

	public function exportValue( object $data ) {

		$da = $this->getValue( $data, [] );
		$nD = [];

		// should be for every entry [e, e]
		foreach ( $da as $d )
			$nD[] = $this->field->exportValue( (object) [
				$this->field->slug => $d
			] );

		return $nD;
	}

	protected function exportData( object $data ) {
		return [ $this->addText, $this->removeText, $this->field->export( (object) [] ) ];
	}

	public function validate( object $input ) {

		$d = $this->getValue( $input );
		if ( !is_string( $d ) )
			return false;

		$d = json_decode( $d );

		if ( !is_array( $d ) )
			return false;

		$slug = $this->field->slug;

		foreach ( $d as $v )
			if ( !$this->field->validate( (object) [
				$slug => $v
			] ) )
				return false;

		return true;

	}

	public function out( object $input ) {
		// array unique should maybe be added?
		$da = json_decode( $this->getValue( $input ) );
		$nD = [];

		// should be for every entry [e, e]
		foreach ( $da as $d )
			$nD[] = $this->field->out( (object) [
				$this->field->slug => $d
			] );

		return $nD;
	}

	// should implement view??
	public function view( object $input ) {

		$data = $this->getValue( $input );

		if ( !is_array( $data ) )
			return [];

		$d = [];
		foreach ( $data as $v )
			$d[] = $this->field->view( (object) [
				$this->field->slug => $v
			] );

		return $d;
	}

}