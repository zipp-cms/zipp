<?php
/*
@package: Zipp
@version: 0.1 <2019-05-28>
*/

namespace Users;

use Core\Module;
use \Error;
use Data\Module as DataModule;

class Users extends Module {

	use DataModule;

	protected $root = false;

	// DbUsers FsUsers
	protected $handlers = [ 'database' => 'Data\DbUsers' ];

	// GETTERS
	public function _getId() {
		return $this->mods->Session->userId;
	}

	public function _getUsername() {
		return $this->mods->Session->username;
	}

	public function _getSurname() {
		return $this->mods->Session->surname;
	}

	public function _getLastname() {
		return $this->mods->Session->lastname;
	}

	public function _getEmail() {
		return $this->mods->Session->email;
	}

	public function _getRole() {
		return $this->mods->Session->role;
	}

	// METHODS
	public function isRoot() {
		return $this->root;
	}

	public function isLoggedIn() {

		if ( $this->root )
			return true;

		$sess = $this->mods->Session;

		return (bool) $sess->get( 'loggedIn', false );

	}

	public function isAdmin() {

		// dont need to check if logged in
		if ( $this->root )
			return true;

		$sess = $this->mods->Session;
		$role = (int) $sess->get( 'role', 0 );

		return $role >= 8;

	}

	// should check
	public function login( string $username, string $password ) {

		$username = lower( $username );

		if ( !$this->validate([ 'username' => $username, 'password' => $password ]) )
			return false;

		$user = $this->handler->getByUsername( $username );

		if ( !$user )
			return false;

		if ( !pwVerify( $password, $user->password ) )
			return false;

		$this->saveToSess( $user );

		return true;

	}

	public function new( string $username, string $password, string $surname, string $lastname, string $email, int $role ) {

		$username = lower( $username );

		if ( !$this->validate([
			'username' => $username,
			'password' => $password,
			'surname' => $surname,
			'lastname' => $lastname,
			'email' => $email,
			'role' => $role
		]) )
			return false;

		$d = $this->handler;

		return $d->insert( $username, pwHash( $password ), $surname, $lastname, $email, $role, 2 );

	}

	public function edit( string $surname, string $lastname, string $email, string $password = null ) {

		if ( !$this->validate([
			'surname' => $surname,
			'lastname' => $lastname,
			'email' => $email
		]) )
			return false;

		if ( !isNil( $password ) )
			$password = pwHash( $password );

		$res = $this->handler->editUser( $this->id, $surname, $lastname, $email, $password );

		if ( !$res )
			return false;

		$sess = $this->mods->Session;
		$sess->surname = $surname;
		$sess->lastname = $lastname;
		$sess->email = $email;

		return true;

	}

	public function install() {

		$d = $this->handler;

		$d->create();

		if ( !$d->exists( 'zippadmin' ) )
			$this->new( 'zippadmin', 'Password!', 'Zipp', 'Admin', 'info@zipp-cms.com', 10 );

	}

	public function logout() {
		$this->mods->Session->destroy();
	}

	public function export() {
		return (object) [
			'id' => $this->id,
			'username' => $this->username,
			'surname' => $this->surname,
			'lastname' => $this->lastname,
			'email' => $this->email,
			'role' => $this->role
		];
	}

	// INIT
	public function onInit() {

		// should i already check if i have db or fsstorage

		if ( !$this->mods->has( 'Router' ) || !$this->mods->Router->isEnabled() )
			$this->root = true;

	}

	public function onInstalling() {
		$this->install();
	}

	// PROTECTED
	protected function saveToSess( object $user ) {

		$sess = $this->mods->Session;

		$sess->loggedIn = true;
		$sess->userId = $user->userId;
		$sess->username = $user->username;
		$sess->surname = $user->surname;
		$sess->lastname = $user->lastname;
		$sess->email = $user->email;
		$sess->role = (int) $user->role;
		$sess->state = (int) $user->state;

	}

	protected function validate( array $a ) {
		foreach ( $a as $k => $v )
			if ( !$this->valid( $k, $v ) )
				return false;
		return true;
	}

	// maybe this should be deleted :)
	protected function valid( string $k, string $s ) {
		switch ( $k ) {
			case 'username':
				return cLen( $s, 1, 25 );
			case 'password':
				return cLen( $s );
			case 'surname':
				return cLen( $s, 1, 30 );
			case 'lastname':
				return cLen( $s, 1, 30 );
			case 'email':
				return cLen( $s, 1, 40 ) && filter_var( $s, FILTER_VALIDATE_EMAIL );
			case 'role':
				$i = (int) $s;
				return $i > 0 && $i <= 10;
			default:
				return false;
		}
	}

}