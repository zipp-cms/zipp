<?php
/*
@package: Zipp
@version: 0.1 <2019-05-28>
*/

namespace Admin;

use Router\Request;
use Ajax\Request as AjaxRequest;
use Core\MagicGet;
use \Exception;

class Page extends MagicGet {

	protected $admin = null;

	protected $mod = null;

	protected $mods = null;

	protected $slug = '';

	protected $section = '';

	protected $nonceKey = '';

	protected $template = null;

	public function __construct( Admin $admin, string $mod ) {

		$this->admin = $admin;
		$this->mods = $this->admin->mods;
		$this->mod = $this->mods->get( $mod );
		$this->templPath = $this->mod->path. 'templ'. DS;
		// here we will only loose context in the context of a specific theme :)
		// and to use the theme for admin pages is probably a wrong thing :)

	}

	public function _getLang() {
		return $this->mod->lang;
	}

	public function _getUsers() {
		return $this->mods->Users;
	}

	// maybe should pass an ajax request
	// or an data request????
	public function onData( DataRequest $req ) {
		return sprintf( 'no method in %s defined', $this->slug );
	}

	public function onAjax( AjaxRequest $req ) {
		$req->error( 'no method defined' );
	}

	public function onRequest( Request $req ) {

		if ( !isNil( $this->template ) ) {
			$this->loadHeader();
			$this->loadTempl( $this->template );
			$this->loadFooter();
		} else
			return false;

	}

	protected function baseData() {
		$l = $this->lang;
		return [
			'activeLang' => $l->getActiveLang(),
			'lang' => $l->getAll(),
			'slug' => $this->slug,
			'section' => $this->section,
			'title' => 'undefined',
			'sections' => $this->admin->getSections()
		];
	}

	public function baseOnData( DataRequest $req ) {

		try {

			$d = $this->onData( $req );

		} catch ( Exception $e ) {
			return $req->error( $e->getMessage() );
		}

		if ( is_array( $d ) )
			return $req->ok( array_merge( $this->baseData(), $d ) );

		$req->error( (string) $d );

	}

	// return html string with a nonce
	public function nonce() {
		return $this->mods->Nonce->newForm( $this->nonceKey );
	}

	public function checkNonce( AjaxRequest $req ) {

		if ( $this->mods->Nonce->checkForm( $this->nonceKey, $req->data ) )
			return true;

		$req->error( $this->admin->lang->nonceIncorrect );
		return false;

	}

	public function newNonce() {
		return $this->mods->Nonce->new( $this->nonceKey );
	}

	public function isAdmin() {
		return $this->users->isAdmin();
	}

	protected function basicVars( array $add = [] ) {
		return array_merge( [
			'a' => $this->admin,
			't' => $this->mods->Time, // should be move i think
			'ajax' => $this->mods->Ajax,
			'r' => $this->mods->Router
		], $add );
	}

	// title should not be used from user input
	protected function loadHeader() {
		includeWithArgs( $this->admin->path. 'templ'. DS. 'header.php', $this->basicVars( [
			'l' => $this->admin->lang,
			'showNav' => !isNil( $this->admin->getSections() )
		] ) );
	}

	// title should not be used from user input
	protected function loadFooter() {
		includeWithArgs( $this->admin->path. 'templ'. DS. 'footer.php', $this->basicVars( [
			'l' => $this->admin->lang
		] ) );
	}

	// this function is not safe for use input
	protected function loadTempl( string $name, array $adds = [] ) {
		includeWithArgs( $this->templPath. $name. '.php',  $this->basicVars( array_merge( [
			'l' => $this->lang
		], $adds ) ) );
	}

	// for nicer "view"
	public function __debugInfo() {
		return [
			'(prot) slug' => $this->slug,
			'(prot) section' => $this->section,
			'(prot) nonceKey' => $this->nonceKey,
			'admin' => '[class]',
			'mod' => '[class]',
			'mods' => '[class]',
			'lang' => '[class]',
			'users' => '[class]'
		];
	}

}