<?php
/*
@package: Zipp
@version: 0.2 <2019-06-01>
*/

namespace Themes;

use Fields\Viewer\Viewer;

class Layout {

	public $slug = '';

	public $name = '';

	public $cache = false;

	public $file = '';

	public $components = [];

	public $fields = [];

	protected $resolvedComps = false;

	

	public function resolveComponents( array $components ) {

		if ( $this->resolvedComps )
			return;

		$this->resolvedComps = true;

		$nComponents = [];
		foreach ( $this->components as $k )
			$nComponents[$k] = $components[$k];

		$this->components = $nComponents;

	}

	public function fillFields( object $page ) {

		$viewers = [];
		foreach ( $this->fields as $f )
			$viewers[$f->slug] = $f->view( $page );

		return new Viewer( $viewers );

	}

	public function fullFill( object $page ) {

		$viewers = [];
		foreach ( $this->fields as $f )
			$viewers[$f->slug] = $f->view( $page );

		foreach ( $this->components as $comp )
			$viewers[$comp->slug] = $comp->fill( $page );

		return new Viewer( $viewers );

	}

	public function getComponent( string $comp ) {
		// return $comp->fill
		return $this->components[$comp] ?? false;
	}

	// INIT
	public function __construct( string $slug, object $cfg, array $fields ) {

		$this->slug = $slug;
		$this->name = $cfg->name ?? 'undefined';
		$this->cache = $cfg->cache ?? false;
		$this->file = $slug; // could remove
		$this->components = $cfg->components ?? [];
		$this->fields = $fields;

	}

}