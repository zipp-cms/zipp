<?php
/*
@package: Zipp
@version: 0.2 <2019-07-01>
*/

namespace Router;

use Core\Module;
use \Error;

// other should register a route
// others should 
class Router extends Module {

	protected $enabled = false;

	protected $interfaces = [];

	protected $reqUri = '';

	protected $basePath = '';

	protected $host = null;

	protected $https = true;

	protected $hosts = [];

	// GETTERS
	public function isEnabled() {
		return $this->enabled;
	}

	// no slash at the beginning only at the end
	public function _getBasePath() {
		return $this->basePath;
	}

	public function _getHost() {
		return $this->host;
	}

	public function _getHttps() {
		return $this->https;
	}

	public function _getHosts() {
		return $this->hosts;
	}

	public function _getHttpVersion() {
		return $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1'; // this variable should always be accessible
	}

	/*public function _getFullUri() {
		// $this->reqUri;
	}*/

	// METHODS
	public function redirect( string $url = '', bool $perm = false ) {
		$this->basicRedirect( $this->url( $url ), $perm );
	}

	public function basicRedirect( string $rawUrl, bool $perm = false ) {

		if ( $perm )
			header( $this->httpVersion. ' 301 Moved Permanently' );

		header( 'Location: '. $rawUrl );
		die;

	}

	// redirect basic if starts with http:// else redirect with $this->url called first
	public function intelligentRedirect( string $url = '', bool $perm = false ) {
		$prot = sprintf( 'http%s://', $this->https ? 's' : '' );
		// if starts with protocol basic redirect
		if ( strpos( $url, $prot ) === 0 )
			return $this->basicRedirect( $url, $perm );
		// else redirect with added url
		return $this->redirect( $url, $perm );
	}

	public function url( string $url = '' ) {
		return $this->buildUrlWithHost( $this->host, $url );
	}

	public function setStatusCode( int $code = 200 ) {
		return http_response_code( $code );
	}

	public function buildUrlWithHost( string $host, string $url = '' ) {
		return sprintf( 'http%s://%s/%s%s', $this->https ? 's' : '', $host, $this->basePath === '/' ? '' : $this->basePath, $url );
	}

	public function register( string $url, string $intCls, string $mod ) {

		if ( !$this->enabled )
			return;

		if ( cLen( $url, 2 ) )
			$url = '/'. $url;

		$k = len( $url );
		if ( !isset( $this->interfaces[$k] ) )
			$this->interfaces[$k] = [];

		$this->interfaces[$k][$url] = [$intCls, $mod];

	}

	public function getMatchingLang( array $supported, string $def ) {

		$defLang = 'en';

		$langs = $this->getSupportedLangs();

		if ( isNil( $langs ) || !has( $langs ) )
			return $def;

		foreach ( $langs as $lang ) {

			// maybe should limit langs that come from the router
			// small chance of ddos ??? how big is the max length of http header
			if ( in_array( $lang, $supported ) )
				return $lang;

		}

		return $def;

	}

	public function getSupportedLangs() {

		if ( !$this->enabled )
			return null;

		$http = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';

		if ( !cLen( $http ) )
			return null;

		$parts = explode( ',', $http );

		$list = [];

		foreach ( $parts as $part ) {

			$ps = explode( ';', trim( $part ) );
			$lng = $ps[0]; // could be * / fr-CH fr en
			$qS = $ps[1] ?? '';
			$q = (float) ( cLen( $qS, 3 ) ? substr( $qS, 2 ) : 1 );
			$q = iMax( uInt( $q * 10 ), 10 );

			$list[$q] = $lng;

		}

		// sort langs
		krsort( $list, SORT_NUMERIC );

		return array_values( $list );

	}

	// INITIALIZE
	public function onInit() {

		$uri = $_SERVER['REQUEST_URI'] ?? null;

		if ( isNil( $uri ) )
			return;

		$this->enabled = true;

		// setup properties
		$this->setupProps();

		$this->checkHost();

		$this->reqUri = $this->parseUri( $uri );

	}

	protected function setupProps() {

		// basePath
		$cfg = $this->mods->Configs->open( 'main' );

		// no slash at the beginning only at the end
		$this->basePath = $cfg->get( 'basePath', '/' );
		$this->hosts = $cfg->get( 'allowedHosts', [] );
		$this->https = $cfg->get( 'https', true );

	}

	protected function checkHost() {

		// maybe we need to change this to get around the problem of installation
		// oh im thinking, maybe the first stuff you need to do online???
		if ( !has( $this->hosts ) )
			throw new Error( 'Please define the <allowedHosts> in the main configs!' );

		$reqHost = $_SERVER['HTTP_HOST'] ?? '';
		if ( !in_array( $reqHost, $this->hosts ) ) {
			$this->host = $this->hosts[0];
			return $this->redirect();
		}

		$this->host = $reqHost;

		$https = isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on';
		if ( $this->https !== $https )
			return $this->redirect();

		// if the site is accessed with https but it is configured as no https
		// we wan't to set the site as https
		$this->https = $https;

	}

	protected function parseUri( string $uri ) {

		$uri = parseUri( $uri );

		$basePath = $this->basePath;
		if ( $basePath === '/' ) {
			if ( $uri === '' || $uri[0] !== '/' )
				$uri = '/'. $uri;
			return $uri;
		}

		$uBase = substr( $uri, 0, len( $basePath ) );
		if ( $uBase !== $basePath )
			throw new Error( sprintf( 'Please reconfigure the basePath in the configs! %s !== %s', $uBase, $basePath ) );

		$uri = substr( $uri, len( $basePath ) );
		return ( $uri === '' || $uri === '/' ) ? '/' : '/'. $uri;

	}


	// START
	public function onStart() {

		if ( !$this->enabled )
			return;

		$ints = &$this->interfaces;
		krsort( $ints, SORT_NUMERIC );

		$uri = $this->reqUri;
		$ul = len( $uri );

		foreach ( $ints as $l => $is ) {

			if ( $l > $ul )
				continue;

			foreach ( $is as $u => $ar ) {

				if ( substr( $uri, 0, $l ) !== $u )
					continue;

				$uri = rtrim( substr( $uri, $l ), '/' ). '/';

				// should build the interactor
				$int = new $ar[0]( $this->mods->get( $ar[1] ) );
				
				return $int->on( new Request( $this, $uri ) );

			}

		}

		$req = new Request( $this, $uri );

		$req->error( 404, 'no interactor found' );

	}

}