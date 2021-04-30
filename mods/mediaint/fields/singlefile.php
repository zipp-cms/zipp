<?php
/*
@package: Zipp
@version: 0.1 <2021-01-27>
*/

namespace MediaInt\Fields;

use \Error;
use Fields\Field;
use Media\Media;

class SingleFile extends Field {

	public $type = 'singlefile';

	public $default = '';

	protected $select = '';

	//protected $media = null;

	protected $notAllowed = '';

	protected $allowed = [];

	protected function parseCfg( object $cfg ) {
		$this->select = $cfg->selectBtn ?? 'lang.selectBtn';
		$this->notAllowed = $cfg->notAllowed ?? 'lang.notAllowed';
		$this->allowed = $cfg->allowed ?? Media::getAllowedExtensions();
	}

	// gets returned to javascript
	public function exportValue( object $data ) {

		$val = $this->getValue( $data );
		[$id, $lang] = self::parseValue( $val );

		$viewData = null;

		if ( $id ) {
			$media = Media::getInstance();
			$itm = $media->getById( $id );

			if ( $itm )
				$viewData = $itm->exportShort();
		}

		return $viewData;
	}

	// gets returned to javascript
	protected function exportData( object $data ) {
		return [ $this->select, $this->notAllowed, $this->allowed ];
	}

	// we need a required setting
	// gets called before inserting it into the database
	public function validate( object $in ) {
		$required = $this->sett->req ?? false;

		// maybe should validate if the image is valid
		// the type doenst get validated here, thats not really a security issue

		$d = $this->getValue( $in );

		[$id, $lang] = self::parseValue( $d );

		if ( !$id || !$lang )
			return !$required || false;

		// mediaId123
		$media = substr( $d, 0, 7 );

		return $media === 'mediaId' && $id > 0;

	}

	public function view( object $data ) {

		$d = $this->getValue( $data );

		[$id, $lang] = self::parseValue( $d );

		if ( !$id )
			return false;

		$media = Media::getInstance();
		return $media->getById( $id, $lang );

	}

	// expects a string but checks first
	// returns [(int) id, (string) lang]
	// or [null, null]
	protected static function parseValue( $v ) {

		if ( !is_string( $v ) )
			return [null, null];

		$parts = explode( '|', $v );

		if ( !has( $parts ) )
			return [null, null];

		$mediaId = $parts[0];
		$id = null;
		if ( is_string( $mediaId ) && cLen( $mediaId, 7 ) )
			$id = (int) substr( $mediaId, 7 );

		$lang = null;
		if ( isset( $parts[1] ) && is_string( $parts[1] ) )
			$lang = $parts[1];

		return [$id, $lang];
	}

}