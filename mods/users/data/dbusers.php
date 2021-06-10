<?php
/*
@package: Zipp
@version: 0.1 <2019-05-28>
*/

namespace Users\Data;

use Database\Table;
use \PDO;

class DbUsers extends Table {

	protected $_name = 'users';

	protected $fields = [
			'userId' => 'INT UNSIGNED NOT NULL AUTO_INCREMENT',
			'username' => 'VARCHAR(25) NOT NULL',
			'password' => 'VARCHAR(255) NOT NULL',
			'surname' => 'VARCHAR(30) NOT NULL',
			'lastname' => 'VARCHAR(30) NOT NULL',
			'email' => 'VARCHAR(40) NOT NULL',
			'role' => 'TINYINT UNSIGNED NOT NULL',
			'state' => 'TINYINT UNSIGNED NOT NULL'
		];
	// role 0 - 10
	// viewer = 1
	// editor = 4
	// owner = 6
	// admin = 8
	// root = 10
	// state 0:deleted 1:inactive 2:active

	protected $indexes = 'PRIMARY KEY(`userId`), UNIQUE(`username`)';

	public function getByUsername( string $username ) {

		$pre = $this->getByKey( 'username', $username );

		return $pre->fetchObject();

	}

	public function editUser( int $id, string $surname, string $lastname, string $email, string $password = null ) {

		$ar = [
			'surname' => $surname,
			'lastname' => $lastname,
			'email' => $email
		];

		if ( !isNil( $password ) )
			$ar['password'] = $password;

		return $this->updateById( $id, $ar );

	}

	public function count() {

		$sql = sprintf( 'SELECT COUNT(`username`) as count FROM %s', $this->name );
		$pre = $this->query( $sql );

		if ( !$pre->execute() )
			throw new Error( 'Could not check how many users exist' );

		$obj = $pre->fetchObject();

		return $obj ? $obj->count : 0;

	}

}