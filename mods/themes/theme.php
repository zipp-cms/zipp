<?php
/*
@package: Zipp
@version: 0.1.1 <2021-01-27>
*/

namespace Themes;

use Core\MagicGet;
use Router\Request;
use Pages\Page;
use Pages\PageQuery;
use \Error;

use Fields\Viewer\ArrayViewer;

use MagmaMinifier\Minifier;
use MagmaCSS\Engine as CSSEngine;
use MagmaCfg\Engine as CfgEngine;
use MagmaTemplate\Engine;

class Theme extends MagicGet {

	protected $themes = null;

	protected $mods = null;

	protected $slug = '';

	protected $path = '';

	protected $assets = [];

	protected $components = [];

	protected $layouts = [];

	protected $settings = [];

	protected $pageCats = [];

	protected $activeEngine = null;

	protected $activeLayout = null;

	protected $activeRequest = null;

	protected $dependencies = [];

	// GETTERS

	public function _getMods() {
		return $this->mods;
	}

	public function _getLang() {
		return $this->mods->Langs->open( $this->path, 't'. $this->slug );
	}

	public function _getAssets() {
		return $this->assets;
	}
	
	public function _getComponents() {
		return $this->components;
	}
	
	public function _getLayouts() {
		return $this->layouts;
	}
	
	public function _getSettings() {
		return $this->settings;
	}
	
	public function _getPageCats() {
		return $this->pageCats;
	}

	public function _getTmpPath() {
		return TMP_PATH. $this->slug. DS;
	}

	public function _getTmpUrl() {
		return $this->mods->Router->url( 'tmp/'. $this->slug. '/' );
	}

	public function _getActiveData() {
		if ( isNil( $this->activeEngine ) )
			return null;
		return (object) $this->activeEngine->data;
	}

	// METHODS
	public function url( string $url ) {
		return $this->mods->Router->url( 'user/themes/'. $this->slug. '/'. $url );
	}

	public function baseUrl( string $url ) {
		return $this->mods->Router->url( $url );
	}

	public function redirect( string $url = '' ) {
		return $this->mods->Router->intelligentRedirect( $url );
	}

	public function getField( string $key ) {
		return $this->mods->Fields->getField( $key );
	}

	public function getAssets( string $slug ) {

		$list = $this->assets[$slug] ?? [];
		if ( !has( $list ) )
			return [];

		if ( ( $slug !== 'css' && $slug !== 'js' ) || ( DEBUG && $slug === 'js' ) )
			return $this->addAssetsUrl( $slug, $list );

		$mgTmpPath = $this->tmpPath. 'mgcss'. DS;
		$slugTmpPath = $this->tmpPath. $slug. DS;
		$assetsPath = $this->path. 'assets'. DS. $slug. DS;

		$mgCss = new CSSEngine( $mgTmpPath, DEBUG );

		if ( DEBUG ) {

			// slug is css
			
			$tmpUrl = $this->tmpUrl. 'mgcss/';

			$nList = [];
			foreach ( $list as $l ) {
				if ( $l[1] === 'mgcss' )
					$url = $tmpUrl. $mgCss->go( $assetsPath. $l[0]. '.mgcss', str_replace( ['\\', '/'], ['_', '_'], $l[0] ) );
				else
					$url = $this->url( sprintf( 'assets/css/%s.css', $l[0] ) );
				$nList[] = $url;
			}

			return $nList;

		}

		$paths = [];
		foreach ( $list as $l ) {
			if ( $slug === 'css' && $l[1] === 'mgcss' )
				$paths[] = $mgTmpPath. $mgCss->go( $assetsPath. $l[0]. '.mgcss' );
			else
				$paths[] = $assetsPath. $l[0]. '.'. $slug;
		}

		$minifier = new Minifier( $slugTmpPath );

		if ( $slug === 'css' )
			$file = $minifier->css( $paths, 'v1' );
		else
			$file = $minifier->js( $paths, 'v1' );

		return [$this->tmpUrl. $slug. '/'. $file];

	}

	protected function addAssetsUrl( string $slug, array $list ) {
		$nList = [];
		foreach ( $list as $l )
			$nList[] = $this->url( sprintf( 'assets/%s/%s.%s', $slug, $l[0], $slug ) );
		return $nList;
	}

	public function renderComp( string $k ) {

		$compEngine = $this->newEngine();
		$this->engineAddFn( $compEngine );
		$comp = $this->activeLayout->getComponent( $k );

		if ( !$comp )
			return;

		$data = $this->activeEngine->data;
		$data['ctn'] = $comp->fill( $data['page'] );

		$compEngine->go( 'components/'. $comp->file, $data, DEBUG );

	}

	public function newEngine() {
		return new Engine( $this->path, TMP_PATH. $this->slug. DS );
	}

	public function newPageQuery() {
		return new PageQuery( $this->mods->Pages, $this );
	}

	protected function engineAddFn( Engine $eng ) {
		$eng->addFn( 'comp', '$theme->renderComp' );
		$eng->addFn( 'url', '$theme->baseUrl' );
		$eng->addFn( 'assets', '$theme->getAssets' );
		$eng->addFn( 'newQuery', '$theme->newPageQuery' );
		$eng->addFn( 'redirect', '$theme->redirect' );
		/*$this->activeEngine->addFn( 'baseUrl', '$theme->baseUrl' );
		$this->activeEngine->addFn( 'lurl', '$theme->langUrl' );*/
	}

	// returns a layout
	public function resolvePage( Page $page ) {

		$this->completeUrlOnPage( $page );

		return $this->loadLayoutByPage( $page );
	}

	public function loadLayoutByPage( Page $page ) {

		$ly = $this->layouts[$page->layout] ?? null;
		if ( isNil( $ly ) )
			throw new Error( sprintf( 'could not find layout (%s)', $ly ) );

		$ly->resolveComponents( $this->components );

		return $ly;
	}

	public function completeUrlOnPage( Page $page ) {
		$page->completeUrl( $this->mods->Router->url(), $this->mods->SiteInt->multilingual );
	}

	public function displayPage( Page $page, Request $req ) {

		/*if ( $this->mods->has( 'Logs' ) )
			$this->mods->Logs->log( 'req', $req->fullUri );*/

		$this->logPageRequest( $req );

		$layout = $this->resolvePage( $page );

		// all settings
		$site = $this->mods->Site->getView( $page->lang );

		$objs = [
			'site' => $site,
			'page' => $page,
			'ctn' => $layout->fillFields( $page ),
			'theme' => $this
		];

		foreach ( $this->settings as $k => $sett )
			$objs[$sett->slug] = $sett->fill( $site );

		// echo '<!-- available keys: '. implode( ', ', array_keys( $objs ) ). ' -->';

		$this->activeEngine = $this->newEngine();
		$this->activeLayout = $layout;
		$this->activeRequest = $req;

		// component in component not supported at the moment
		$this->engineAddFn( $this->activeEngine );

		// Cache
		$c = false;
		if ( $layout->cache && $this->mods->has( 'Cache' ) )
			$c = $this->mods->Cache;

		if ( $c )
			$c->start();

		$this->activeEngine->go( 'layouts/'. $layout->file, $objs, DEBUG );

		if ( $c )
			$c->stop();

	}

	protected function logPageRequest( Request $req, string $custom = '' ) {

		if ( $this->mods->has( 'Users' ) && $this->mods->Users->isLoggedIn() )
			return;

		if ( $this->mods->has( 'Logs' ) )
			$this->mods->Logs->log( 'req', $req->fullUri, $custom );

	}

	public function contentChanged( string $key, $data = null ) {
		
		if ( !$this->mods->has( 'Cache' ) )
			return;

		$this->mods->Cache->clear();

	}

	public function error404( string $lang, Request $req ) {


		$this->logPageRequest( $req, 'error404' );

		// set status code
		$req->router->setStatusCode(404);

		define( 'DONT_OUTPUT_TIME', true );

		// we need to stop here
		// TODO: maybe check for a default layout that shows an error 404 Page

		//echo 'Error 404'. EOL;
		//echo $lang. ' '. $req->uri. EOL;

		// $engine = $this->newEngine();

		// $this->newEngine();

		// $this->activeEngine->go( 'layouts/'. $ly->file, $objs, $this->debug );

	}

	// INIT
	public function __construct( string $slug, Themes $themes ) {

		$this->themes = $themes;
		$this->mods = $themes->mods;
		$this->slug = $slug;
		$this->path = $themes->themesPath. $slug. DS;

		foreach ( array_merge( ['pages', 'site', 'router', 'langs'], $this->dependencies ) as $dep )
			if ( !$this->mods->has( $dep ) )
				throw new Error( sprintf( 'the Theme (%s) needs (%s) but could not find it', $this->slug, $dep ) );

		$this->onInit();

	}

	public function onInit() {}

	// PROTECTED
	protected function loadConfig() {

		$eng = new CfgEngine( TMP_PATH. 'mgcfg'. DS, DEBUG );
		$cfg = $eng->go( $this->path. $this->slug. '.mgcfg' );

		// parse cfg
		// assets
		$assets = $cfg->assets ?? [];
		foreach ( $assets as $slug => $files )
			$this->newAssets( $slug, $files );

		// components
		$comps = $cfg->components ?? [];
		foreach ( $comps as $slug => $comp )
			$this->newComponent( $slug, $comp );

		// layouts
		$layouts = $cfg->layouts ?? [];
		foreach ( $layouts as $slug => $layout )
			$this->newLayout( $slug, $layout );

		// settings
		$settings = $cfg->settings ?? [];
		foreach ( $settings as $slug => $setting )
			$this->newSetting( $slug, $setting );

		// page categories
		$pageCats = $cfg->pageCategories ?? [];
		foreach ( $pageCats as $slug => $cats )
			$this->newPageCat( $slug, $cats );

	}

	// asset files cannot have a dot in them
	protected function newAsset( string $slug, string $file ) {

		$parts = explode('.', $file);

		if ( !isset( $this->assets[$slug] ) )
			$this->assets[$slug] = [];

		$this->assets[$slug][] = [$parts[0], $parts[1] ?? null];

	}

	protected function newAssets( string $slug, array $files ) {

		foreach ( $files as $file )
			$this->newAsset( $slug, $file );

	}

	protected function convertCfgLangs( object &$cfgs ) {
		foreach ( $cfgs as $slug => &$cfg ) {
			if ( is_string( $cfg ) && $cfg[0] === '\'' )
				$cfg = $this->lang->{substr( $cfg, 1 )};
		}
	}

	protected function convertCfgFields( object $fields ) {

		$nFields = [];
		foreach ( $fields as $slug => $cfg ) {

			$this->convertCfgLangs( $cfg );

			// for repeatable fields
			if ( isset( $cfg->field ) )
				$cfg->field = $this->convertCfgFields( $cfg->field )[0];

			if ( isset( $cfg->fields ) )
				$cfg->fields = $this->convertCfgFields( $cfg->fields );

			// add support for lang in options
			if ( isset( $cfg->options ) )
				$this->convertCfgLangs( $cfg->options );

			$type = $cfg->type ?? 'Text';
			$field = $this->getField( $type );
			if ( isNil( $field ) )
				continue;
			$nFields[] = new $field( $slug, $cfg );

		}

		return $nFields;

	}

	protected function newComponent( string $slug, object $comp ) {

		$this->convertCfgLangs( $comp );

		$fields = $comp->fields ?? (object) [];
		$fields = $this->convertCfgFields( $fields );

		// , string $desc = null, array $fields
		$this->components[$slug] = new Component( $slug, $comp, $fields );

	}

	protected function newLayout( string $slug, object $layout ) {

		$this->convertCfgLangs( $layout );

		$fields = $layout->fields ?? (object) [];
		$fields = $this->convertCfgFields( $fields );

		$this->layouts[$slug] = new Layout( $slug, $layout, $fields );

	}

	protected function newSetting( string $slug, object $setting ) {

		$this->convertCfgLangs( $setting );

		$fields = $setting->fields ?? (object) [];
		$fields = $this->convertCfgFields( $fields );

		// theme settings page
		$this->settings['tsp-'. $slug] = new Settings( $slug, $setting, $fields );

	}

	protected function newPageCat( string $slug, object $cats ) {

		$this->convertCfgLangs( $cats );

		// theme pagecat
		$this->pageCats['tpc-'. $slug] = (object) [
			'name' => $cats->pluralName ?? 'undefined',
			'singular' => $cats->singleName ?? 'undefined',
			'layouts' => $cats->layouts ?? []
		];

	}

	// MAGIC
	public function __debugInfo() {
		return [
			'lookat' => 'mods/themes/theme.php'
		];
	}

}