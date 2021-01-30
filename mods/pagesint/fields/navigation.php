<?php
/*
@package: Zipp
@version: 0.2 <2019-07-02>
*/

namespace PagesInt\Fields;

use Fields\Field;
use PagesInt\Pages;

class Navigation extends Field {

	public $type = 'navigation';

	protected $layouts = null;

	protected $lang = [];

	protected $pages = [];

	protected $resolveFull = false;

	public function exportValue( object $data ) {

		$d = $this->getValue( $data, [] );
		if ( !is_array( $d ) )
			$d = [];
		$d = $this->recuConvertTo( $d );

		return $d;

	}

	protected function parseCfg( object $cfg ) {
		$this->layouts = $cfg->layouts ?? [];
		$this->lang = $this->fillMissingLang( $cfg );
		$this->resolveFull = ( $cfg->resolve ?? 'short' ) === 'full';
	}

	protected function fillMissingLang( object $cfg ) {
		return (object) [
			'addBtn' => $cfg->addBtn ?? 'lang.addBtn',
			'selectTitle' => $cfg->selectTitle ?? 'lang.selectTitle',
			'selectBtn' => $cfg->selectBtn ?? 'lang.selectBtn',
			'selectCancel' => $cfg->selectCancel ?? 'lang.selectCancel'
		];
	}

	protected function getPages() {
		$ly = null;
		if ( has( $this->layouts ) )
			$ly = $this->layouts;
		return Pages::getTitlesByLayoutsCookieLang( $ly );
	}

	protected function exportData( object $data ) {

		$pages = $this->getPages();
		return [ $pages, $this->lang ];

	}

	// we need a required setting
	// mh... maybe i need to rethink the fields stuff
	public function validate( object $data ) {

		$d = $this->getValue( $data );
		$d = json_decode( $d );

		if ( !is_array( $d ) )
			return false;

		return $this->recuValidate( $d );

	}

	public function recuValidate( array $data ) {
		foreach ( $data as $d )
			if ( !is_int( (int) $d[0] ) || has( $d[1] ) && !$this->recuValidate( $d[1] ) )
				return false;

		return true;
	}

	public function out( object $data ) {
		$data = json_decode( $this->getValue( $data ) );
		return $this->recuConvertFrom( $data );
	}

	// converts from [(int), [..]] to [ctnId(int), [..]]
	protected function recuConvertFrom( array $data ) {
		$nData = [];
		foreach ( $data as $d )
			$nData[] = ['ctnId'. $d[0], $this->recuConvertFrom( $d[1] )];
		return $nData;
	}

	public function view( object $data ) {

		$data = $this->getValue( $data, [] );

		if ( !is_array( $data ) || !has( $data ) )
			return [];

		$data = $this->recuConvertTo( $data );

		$ids = [];
		$this->recuIds( $data, $ids );
		$ids = array_keys( $ids );

		if ( $this->resolveFull )
			$pages = Pages::getNavigationFullPagesByIds( $ids );
		else
			$pages = Pages::getNavigationPagesByIds( $ids );

		// id => ctn

		$nData = $this->recuData( $data, $pages )[0];

		return $nData;

	}

	// converts from [ctnId(int), [..]] to [(int), [..]]
	protected function recuConvertTo( array $data ) {
		$nData = [];
		foreach ( $data as $d )
			$nData[] = [(int) substr( $d[0], 5 ), $this->recuConvertTo( $d[1] )];
		return $nData;
	}

	public function recuIds( array $data, array &$ids ) {
		foreach ( $data as $d ) {
			$ids[(int) $d[0]] = true;
			if ( has( $d[1] ) )
				$this->recuIds( $d[1], $ids );
		}
	}

	protected function recuData( array $data, array $pages ) {

		$nData = [];
		$active = false;
		foreach ( $data as $d ) {

			$id = (int) $d[0];
			$ctn = $pages[$id] ?? false;
			if ( !$ctn )
				continue;

			$obj = (object) [
				'pageId' => $ctn->pageId,
				'title' => $ctn->title,
				'url' => $ctn->url,
				'lang' => $ctn->lang,
				'active' => $ctn->active,
				'ctn' => $ctn->ctn ?? null,
				'layout' => $ctn->layout ?? null,
				'childrenActive' => false,
				'children' => []
			];

			if ( $ctn->active )
				$active = true;

			if ( has( $d[1] ) ) {

				$nD = $this->recuData( $d[1], $pages );
				$obj->children = $nD[0];

				if ( $nD[1] ) {
					$active = true;
					$obj->childrenActive = true;
				}

			}

			$nData[] = $obj;

		}

		return [$nData, $active];

	}

}