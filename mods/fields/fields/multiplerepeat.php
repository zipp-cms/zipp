<?php
/*
@package: Zipp
@version: 1.0 <2019-06-01>
*/

namespace Fields\Fields;

use \Error;
use Fields\Viewer\Viewer;
use Fields\Field;

class MultipleRepeat extends Field {

	public $type = 'multiplerepeat';

	public $default = '';

	protected $fields = null;

	protected $addText = '';

	protected $removeText = '';

	// PROTECTED
	protected function parseCfg( object $cfg ) {

		$this->fields = $cfg->fields ?? null;
		if ( !is_array( $this->fields ) || !has( $this->fields ) )
			throw new Error( sprintf( 'the multiple repeat field (%s) is missing a field (fields) to repeat', $this->slug ) );

		$this->addText = $cfg->addBtn ?? 'addBtn';
		$this->removeText = $cfg->removeBtn ?? 'removeBtn';

	}

	public function exportValue( object $data ) {

		$da = $this->getValue( $data, [] );
		$newData = [];

		foreach ( $da as $d ) {
			$nD = [];
			foreach ( $this->fields as $k => $f )
				$nD[] = $f->exportValue( (object) [
					$f->slug => $d[$k] ?? null
				] );
			$newData[] = $nD;
		}

		return $newData;

	}

	protected function exportData( object $data ) {

		$nD = [];
		foreach ( $this->fields as $f )
			$nD[] = $f->export( (object) [] );

		return [ $this->addText, $this->removeText, $nD ];

	}

	public function validate( object $input ) {

		$d = $this->getValue( $input );
		if ( !is_string( $d ) )
			return false;

		$d = json_decode( $d );

		if ( !is_array( $d ) )
			return false;

		// $slug = $this->field->slug;

		foreach ( $d as $dd ) {
			foreach ( $this->fields as $k => $f )
				if ( !$f->validate( (object) [
					$f->slug => $dd[$k] ?? null
				] ) )
					return false;
		}
			

		return true;

	}

	public function out( object $input ) {

		$data = $this->getValue( $input );
		$data = json_decode( $data );

		$newData = [];

		foreach ( $data as $d ) {
			$nD = [];
			foreach ( $this->fields as $k => $f )
				$nD[] = $f->out( (object) [
					$f->slug => $d[$k] ?? null
				] );
			$newData[] = $nD;
		}
			

		return $newData;
	}

	public function view( object $input ) {

		$data = $this->getValue( $input );
		if ( !is_array( $data ) )
			return [];

		$nD = [];
		foreach ( $data as $dd ) {
			$d = [];
			foreach ( $this->fields as $k => $f )
				$d[$f->slug] = $f->view( (object) [
					$f->slug => $dd[$k] ?? null
				] );
			$nD[] = new Viewer( $d );
		}

		return $nD;
		// return new Viewer\Boolean( $this->getD( $data ) );
	}

}