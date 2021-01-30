<?php
/*
@package: Zipp
@version: 0.2 <2019-06-01>
*/

namespace Themes;

use Fields\Viewer\Viewer;

class Component {

	public $slug = '';

	public $name = '';

	public $desc = null;

	public $file = '';

	public $fields = [];

	public function __construct( string $slug, object $comp, array $fields ) {

		$this->slug = $slug;
		$this->name = $comp->name ?? 'not defined';
		$this->desc = $comp->desc ?? null;
		$this->file = $slug; // could remove this later

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