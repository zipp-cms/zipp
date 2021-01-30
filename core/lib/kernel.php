<?php
/*
@package: Zipp
@version: 0.2 <2019-07-01>
*/

namespace Core;

use \Error;

class KERNEL {

	// the modules instance
	protected static $mods = null;

	protected static $stop = false;

	protected static $debug = true;

	public static function init() {

		// basic setup
		self::setup( DEBUG );

		// check writeaccess, extension, php version
		self::checkBasic();

		// load modules
		self::$mods = new Modules;

	}

	public static function start() {

		self::init();

		// loop through every stage
		if ( INSTALLING )
			self::install();
		else
			self::run();

	}

	public static function stop() {
		self::$stop = true;
	}

	// returns the instance from mods if possible
	// this should be used only if necessary
	public static function getInstance( string $mod ) {
		return self::$mods->get( $mod );
	}

	public static function checkExt( string $ext ) {

		if ( !extension_loaded( $ext ) )
			throw new Error( 'Please activate the extension '. $ext );

	}

	// PROTECTED
	protected static function setup( bool $showErrors ) {

		// show errors
		error_reporting( E_ALL );
		ini_set( 'display_errors', $showErrors );
		if ( !$showErrors ) {
			ini_set( 'log_errors', true );
			ini_set( 'error_log', TMP_PATH. 'errors.log' );
			ini_set( 'log_errors_max_len', 1024 );
		}

		// setup time
		date_default_timezone_set( 'UTC' );
		setlocale( LC_ALL, 'en_US.UTF-8' );

	}

	protected static function checkBasic() {

		// check if we have write permission
		if ( !is_writable( DIR. 'index.php' ) )
			throw new Error( 'Please give me write permissions' );

		// check php version
		if ( version_compare( phpversion(), '7.2.0', '<' ) )
			throw new Error( sprintf( 'Please update your php version (min 7.2 is required). Your running version %s.', phpversion() ) );

		self::checkExt( 'Core' );
		self::checkExt( 'date' );
		self::checkExt( 'json' );

	}

	protected static function run() {

		while ( self::$mods->nextStage() ) {

			self::$mods->triggerEvent( 'init' );

			if ( !self::$stop )
				self::$mods->triggerEvent( 'start' );

			if ( self::$stop )
				break;

		}

		if ( !self::$stop )
			self::$mods->triggerEvent( 'end', false );


		self::$mods->triggerEvent( 'termination', false );

	}

	protected static function install() {

		// should create .htacess (file)
		// after every mod that has event installing has given is ok
		// echo 'Installing isnt implemented yet';
		while ( self::$mods->nextStage() )
			self::$mods->triggerEvent( 'installing' );

		$extensions = [
			'css,js,map,mjs', // dev
			'ico,png,jpg,svg,gif', // imgs
			'mp3,wav,ogg', // music
			'mp4,mov,ogv', // video
			'doc,docx,txt,rtf,pdf', // docs
			'ttf,otf,woff,woff2,eot', // fonts
			'zip', 'rar', 'gz' // archives
		];
		$extensions = explode( ',', implode( ',', $extensions ) );

		// or
		// dont allow extensions
		// php, cfg, json, sql, htaccess, mgcfg, html

		$lines = [
			'# @package: Zipp',
			'# @version: 0.2.1 <2019-08-07>',
			'# @installedOn: <'. date('Y-m-d'). '>',
			'',
			'RewriteEngine On',
			'RewriteCond %{REQUEST_URI} !\.('. implode( '|', $extensions ). ')$',
			'RewriteRule .* index.php [L]'
		];

		FS::write( HTACCESS_FILE, implode( EOL, $lines ) );

		echo 'installed';

	}

}
/*
# @package: Zipp
# @version: 0.1 <2019-05-27>

RewriteEngine On

# Rewrite if not an allowed file
# imgs: css|js|ico|png|jpg|svg|gif
# music: mp3|wav
# video: mp4|mov
# docs: doc|docx|txt|rtf|pdf
# fonts: ttf|otf|woff|woff2|eot
RewriteCond %{REQUEST_URI} !\.(css|js|ico|png|jpg|svg|gif|mp3|wav|mp4|mov|doc|docx|txt|rtf|pdf|ttf|otf|woff|woff2|eot)$
RewriteRule .* index.php [L]
*/