<?php
/*
@package: Zipp
@version: 0.1 <2019-05-28>
*/

namespace Database;

use Core\Module;
use \PDO;
use \Error;

class Database extends Module {

	protected $pdo = null;

	protected $prefix = null;

	public function _getPrefix() {
		if ( isNil( $this->prefix ) ) {
			$cfg = $this->mods->Configs->open( 'database' );
			$this->prefix = $cfg->get( 'prefix', '' );
		}
		return $this->prefix;
	}

	public function _getPdo() {

		if ( !isNil( $this->pdo ) )
			return $this->pdo;

		$cfg = $this->mods->Configs->open( 'database' );

		$host = $cfg->get( 'host' );
		$name = $cfg->get( 'name' );
		$user = $cfg->get( 'user' );
		$password = $cfg->get( 'password' );

		if ( isNil( $host ) || isNil( $name ) || isNil( $user ) || isNil( $password ) )
			throw new Error( 'Database Configuration needs <host> <name> <user> and <password>' );

		$connParams = sprintf( 'mysql:host=%s;dbname=%s; charset=utf8mb4', $host, $name );

		$this->pdo = new PDO( $connParams, $user, $password );

		$this->pdo->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
		$this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

		return $this->pdo;

	}

}