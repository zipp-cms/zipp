<?php
/*
@package: Zipp
@version: 0.1 <2019-06-12>
*/

namespace MediaInt\Fields;

use \Error;
use Media\Media;

class MultipleFiles extends SingleFile {

	public $type = 'multiplefiles';

	// TODO: maybe should add a max?

	public function exportValue( object $data ) {

		$nData = $this->getValue( $data, [] );
		if ( !is_array( $nData ) )
			$nData = [];

		$nData = array_unique( $nData );
		$media = Media::getInstance();

		$ids = [];
		foreach ( $nData as $d ) {

			[$id, $lang] = self::parseValue($d);
			if ( $id )
				$ids[] = $id;

		}

		if ( !has( $ids ) )
			return [];

		$itms = $media->getByIds( $ids );
		$viewData = [];
		foreach ( $itms as $itm )
			$viewData[] = $itm->exportShort();

		return $viewData;

	}

	// we need a required setting
	public function validate( object $in ) {

		// maybe should validate if the image is valid
		// the type doenst get validated here, thats not really a security issue

		$d = $this->getValue( $in );
		if ( is_string( $d ) )
			$d = json_decode( $d );

		if ( !is_array( $d ) )
			return false;

		foreach ( $d as $m ) {

			[$id, $lang] = self::parseValue( $m );

			if ( !$id || !$lang )
				return false;

			$media = substr( $m, 0, 7 );
			if ( $media !== 'mediaId' || $id <= 0 )
				return false;

		}

		return true;

	}

	public function out( object $data ) {
		$d = $this->getValue( $data );
		if ( is_string( $d ) )
			return json_decode( $d );
		return $d;
	}

	public function view( object $data ) {

		$d = $this->getValue( $data, [] );

		if ( !is_array( $d ) || !has( $d ) )
			return [];

		$media = Media::getInstance();

		$gLang = null;
		$ids = [];
		foreach ( $d as $id ) {
			[$id, $lang] = self::parseValue( $id );
			$ids[] = $id;
			$gLang = $lang;
		}
		
		return $media->getByIds( $ids, $gLang );

	}

}