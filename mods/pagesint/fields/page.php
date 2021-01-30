<?php
/*
@package: Zipp
@version: 0.2 <2020-04-19>
*/

namespace PagesInt\Fields;

use Fields\Field;
use PagesInt\Pages;

class Page extends Field {

	public $type = 'page';

	protected $layouts = [];

	protected $noPage = null;

	protected $resolveFull = false;

	public function validate( object $data ) {

		$d = $this->getValue( $data );

		if ( !is_string( $d ) && !is_int( $d ) )
			return false;

		$id = (int) $d;

		// if it is allowed to have no page allow 0
		if ( !isNil( $this->noPage ) )
			return $id >= 0;

		// else don't allow 0
		return $id > 0;
	}

	// to database
	public function out( object $data ) {
		$d = $this->getValue( $data );
		return 'ctnId'. $d;
	}

	// to template | inputs are database (from out)
	public function view( object $data ) {

		$d = $this->getValue( $data );

		if ( !is_string( $d ) )
			return null;

		$id = (int) substr( $d, 5 );

		if ( $id <= 0 )
			return null;
		/*
		if ( !is_int( $id ) )
			return null;
		*/
		if ( $this->resolveFull )
			$page = Pages::getFieldPageFullById( $id );
		else
			$page = Pages::getFieldPageById( $id );

		return $page;

	}

	public function exportValue( object $data ) {

		$d = $this->getValue( $data );

		if ( !is_string( $d ) || len( $d ) < 6 )
			$d = 'ctnId0';

		$id = (int) substr( $d, 5 );

		return is_int( $id ) ? $id : 0;
	}

	// protected
	protected function parseCfg( object $cfg ) {
		$this->layouts = $cfg->layouts ?? [];
		$this->noPage = $cfg->noPage ?? null;
		$this->resolveFull = ( $cfg->resolve ?? 'short' ) === 'full';
	}

	protected function exportData( object $data ) {
		return [ $this->getPages(), $this->noPage ];
	}

	protected function getPages() {
		$ly = null;
		if ( has( $this->layouts ) )
			$ly = $this->layouts;
		return Pages::getTitlesByLayoutsCookieLang( $ly );
	}

}