<?php
/*
@package: Zipp
@version: 0.1 <2019-06-11>
*/

namespace Media;

use \Error;

class MediaItem {

	// page
	public $id = 0;

	public $lang = 'null'; // 5

	public $name = ''; // max 30 (small, only valid url chars)

	public $type = ''; // max 10 ex. jpg

	// ctn
	public $ctn = '{}';

	public $createdOn = '2019-05-31 09:31:00';

	protected $preUrl = '';

	protected $bLang = '';

	public function __construct( object $itm, string $lang = null, string $preUrl ) {

		$this->id = (int) $itm->mediaId;
		$this->lang = $itm->lang;
		$this->name = $itm->name;
		$this->type = $itm->type;
		$this->ctn = json_decode( $itm->content );
		$this->createdOn = $itm->createdOn;

		$this->preUrl = $preUrl;
		$this->bLang = $lang;

	}

	// get content by lang
	public function alt( string $lang = null ) {
		$lang = $lang ?? $this->bLang;
		if ( isNil( $lang ) )
			throw new Error( 'if you want to use alt you need to define lang on calling the function alt or by calling setLang on this item' );

		return e( $this->ctn->{'alt'. $lang} ?? '' );
	}

	// returns html tag
	public function tag( string $width = null, string $height = null ) {
		return sprintf( '<img src="%s" alt="%s">', $this->src( $width, $height ), $this->alt() );
	}

	// src
	// width and height arent viewed
	public function src( string $width = null, string $height = null ) {
		// cropping should be implemented
		return sprintf( '%s%s.%s?width=%d&height=%d', $this->preUrl, $this->name, $this->type, $width ?? -1, $height ?? -1 );
	}

	public function is( string $cat ) {
		return Media::checkExtCat( $this->type, $cat );
	}

	// resize and return path to media file
	// TODO: add resize
	// s = size
	/*public function size( string $width, string $height = null ) {

	}*/

	public function setLang( string $lang ) {
		$this->bLang = $lang;
	}

	public function getFilename() {
		return sprintf( '%s.%s', $this->name, $this->type );
	}

	public function exportShort() {
		return (object) [
			'id' => $this->id,
			'lang' => $this->lang,
			'name' => $this->name,
			'type' => $this->type,
			'preUrl' => $this->preUrl,
			'cat' => Media::getCat( $this->type )
		];
	}

	public function __get( string $k ) {

		if ( isset( $this->$k ) )
			return $this->$k;

		return $this->ctn->$k ?? null;

	}

}