<?php
/*
@package: Zipp
@version: 0.2 <2019-06-01>
*/

namespace Themes;

use Fields\Viewer\Viewer;

class Settings {

	public $slug = '';

	public $name = '';

	public $fields = [];

	public function __construct( string $slug, object $cfg, array $fields ) {

		$this->slug = $slug;
		$this->name = $cfg->name ?? 'undefined';

		foreach ( $fields as $f )
			$f->slug = $slug. '_'. $f->slug;

		$this->fields = $fields;

	}

	public function fill( object $data ) {

		$viewers = [];
		foreach ( $this->fields as $f )
			$viewers[$this->stripSlug( $f->slug )] = $f->view( $data );

		return new Viewer( $viewers );

	}

	protected function stripSlug( string $slug ) {
		return substr( $slug, len( $this->slug ) + 1 );
	}

}