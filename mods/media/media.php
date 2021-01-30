<?php
/*
@package: Zipp
@version: 0.1 <2019-06-11>
*/

namespace Media;

use Core\Module;
use Core\KERNEL;
use Data\Module as DataModule;
use \Error;

class Media extends Module {

	use DataModule;

	// STATIC
	// dont change this variable
	protected static $allowedExts = [
		'jpg' => ['img', 'image/jpeg'],
		'ico' => ['img', 'image/x-icon'],
		'png' => ['img', 'image/png'],
		'svg' => ['img', 'image/svg+xml'],
		'gif' => ['img', 'image/gif'],

		'mp3' => ['audio', 'audio/mpeg', 'audio/mp3'], // second mimetype for chrome
		'wav' => ['audio', 'audio/wav'],
		'ogg' => ['audio', 'audio/ogg'],

		'mp4' => ['video', 'video/mp4'],
		'mov' => ['video', 'video/quicktime'],
		'ogv' => ['video', 'video/ogg'],

		'doc' => ['doc', 'application/msword'],
		'docx' => ['doc', 'application/vnd.openxmlformats-officedocument. wordprocessingml.document'],
		'txt' => ['doc', 'text/plain'],
		'rtf' => ['doc', 'text/rtf'],
		'pdf' => ['doc', 'application/pdf'],

		'zip' => ['arch', 'application/zip'],
		'rar' => ['arch', 'application/x-rar-compressed'],
		'gz' => ['arch', 'application/gzip']
	];

	// INSTANCE
	protected $interfaces = [];

	protected $handlers = [ 'database' => 'Data\DbMedia' ];
	
	// STATIC METHODS
	public static function checkExt( string $ext, string $type ) {
		if ( !isset( self::$allowedExts[$ext] ) )
			return false;
		if ( self::$allowedExts[$ext][1] === $type )
			return true;
		if ( isset( self::$allowedExts[$ext][2] ) ) // check for secondary mimetype
			return self::$allowedExts[$ext][2] === $type;
		return false;
	}

	// the ext must exists in the above list
	public static function checkExtCat( string $ext, string $cat ) {
		return self::$allowedExts[$ext][0] === $cat;
	}

	public static function getCat( string $ext ) {
		return self::$allowedExts[$ext][0] ?? $ext;
	}

	public static function getAllowedExtensions() {
		return array_keys( self::$allowedExts );
	}

	// this should only be used if nothing else is possible
	public static function getInstance() {
		return KERNEL::getInstance( 'Media' );
	}

	// INSTANCE METHODS
	public function getMediaUrl() {
		return $this->mods->Router->url( 'user/media/' );
	}

	public function getAll( string $lang ) {

		$items = $this->handler->getAllByLang( $lang );

		$url = $this->getMediaUrl();

		$its = [];
		foreach ( $items as $itm )
			$its[] = new MediaItem( $itm, $lang, $url );

		return $its;
	}

	public function getById( int $id, string $lang = null ) {

		$itm = $this->handler->getById( $id );

		if ( !$itm )
			return false;

		return new MediaItem( $itm, $lang, $this->getMediaUrl() );

	}

	public function getByIds( array $ids, string $lang = null ) {

		$itms = $this->handler->getByIds( $ids );
		$mItms = [];

		foreach ( $itms as $itm )
			$mItms[] = new MediaItem( $itm, $lang, $this->getMediaUrl() );

		return $mItms;

	}

	public function edit( int $id, string $lang, array $ctn ) {
		$this->handler->update( $id, $lang, json_encode( $ctn ) );
	}

	public function new( string $name, string $type, int $size ) {
		$id = $this->handler->insert( 'nulll', $name, $type, $size, '{}', now() );
		return new MediaItem( (object) [
			'mediaId' => $id,
			'lang' => 'nulll',
			'name' => $name,
			'type' => $type,
			'content' => '{}',
			'createdOn' => now()
		], null, $this->getMediaUrl() );
	}

	public function delete( int $id ) {
		$this->handler->deleteById( $id );
	}
	
	public function install() {
		$this->handler->create();
	}

	// doenst check if the path exists
	public function getPath() {
		return USER_PATH. 'media'. DS;
	}

	// INIT
	public function onInstalling() {
		$this->install();
	}

}