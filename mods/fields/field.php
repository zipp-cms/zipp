<?php
/*
@package: Zipp
@version: 1.0 <2019-06-01>
*/

namespace Fields;

use \Error;

class Field {

	/// The type which this field represents
	/// should be the class name in lowercase.
	///
	/// Is used in javascript to determine which
	/// javascript class should be used.
	public $type = 'field';

	/// The slug of this field set when creating this field.
	///
	/// ## Example from |theme|.mgcfg
	/// slug
	/// 	type: Field
	public $slug = '';

	/// The name of this field, set by the user
	/// with an attribute `name` in |theme|.mgcfg
	public $name = '';

	/// The description of this field, if any.
	/// Gets set by the user with an attribute `desc`.
	public $desc = '';

	/// Stores all settings attributes mainly:
	/// - req
	/// - max
	/// - min
	public $sett = [];

	/// The default value that should be used if this field
	/// was not previously set.
	public $default = null;

	/// The values that are exported to the javascript implementation.
	public function export( object $data ) {
		return array_merge( [ $this->type, $this->slug, $this->name, $this->desc, $this->sett, $this->exportValue( $data ) ], $this->exportData( $data ) );
	}

	/// Gets called after the field was 
	public function validate( object $input ) { return false; }

	// to database
	public function out( object $input ) {
		return $this->getValue( $input );
	}

	// to template or component
	public function view( object $data ) {
		return $this->out( $data );
	}

	public function exportValue( object $data ) {
		return $this->getValue( $data );
	}

	// INIT
	// if you pass a user variable you need to escape it!!!!
	// cfg needs to be and object or an associative array
	public function __construct( string $slug, $cfg ) {
		$cfg = (object) $cfg;
		$this->slug = $slug;
		$this->name = $cfg->name ?? 'undefined';
		$this->desc = $cfg->desc ?? null;
		$this->sett = (object) ( $cfg->sett ?? [] );
		$this->default = $cfg->default ?? null;
		$this->parseCfg( $cfg );
	}

	// PROTECTED
	protected function parseCfg( object $cfg ) {}

	protected function exportData( object $data ) { return []; }

	protected function getValue( object $data, $def = null ) {
		return $data->{ $this->slug } ?? ( $def ?? $this->default );
	}

}