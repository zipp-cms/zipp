<?php
/*
@package: Zipp
@version: 0.1 <2019-05-28>
*/

namespace Database;

use \PDO;
use \Error;
use Core\Modules;
use Core\MagicGet;

class Table extends MagicGet {

	protected $database = null;

	// protected $mod = null;

	// the table name
	protected $_name = '';

	// the fields in the table
	// like = 'userId' => 'INT UNSIGNED NOT NULL AUTO_INCREMENT'
	protected $fields = [];

	// every indexes like
	// PRIMARY KEY(`userId`), UNIQUE(`username`)
	protected $indexes = '';

	// GETTERS
	public function _getPdo() {
		return $this->database->pdo;
	}

	public function _getName() {
		return $this->database->prefix. $this->_name;
	}

	public function __construct( Modules $mods ) {
		$this->database = $mods->Database;
		// $this->mod = $mod;
	}

	// a static query to the database
	// should not include user data
	public function query( string $sql ) {
		return $this->pdo->query( $sql );
	}

	// prepares a query to the database
	public function prepare( string $sql ) {
		return $this->pdo->prepare( $sql );
	}

	// gets the first field
	public function key() {

		reset( $this->fields );
		return key( $this->fields );

	}

	// returns an string with every key
	public function keys() {

		$l = [];

		foreach ( $this->fields as $k => $v )
			$l[] = sprintf( '`%s`', $k );
			
		return implode( ', ', $l );

	}

	// creates the table in the database
	public function create() {

		$s = [];

		foreach ( $this->fields as $k => $f )
			$s[] = sprintf( '`%s` %s', $k, $f );

		$sql = sprintf( 'CREATE TABLE IF NOT EXISTS `%s` ( %s, %s ) ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci', $this->name, implode( ', ', $s ), $this->indexes );
		$pre = $this->query( $sql );

		if ( !$pre->execute() )
			throw new Error( sprintf( 'Could not create "%s"', $this->name ) );

	}

	// drops the table if it exists
	public function drop() {

		$sql = sprintf( 'DROP TABLE IF EXISTS `%s`', $this->name );
		$pre = $this->query( $sql );

		if ( !$pre->execute() )
			throw new Error( sprintf( 'Could not drop "%s"', $this->name ) );

	}

	// gets everything with if you wand limit and offset
	// maybe add a yield function ???
	public function getAll( int $limit = -1, int $offset = 0 ) {

		$l = $limit <= 0 ? '' : ' LIMIT :l';
		$o = !cLen( $l ) || $offset <= 0 ? '' : ' OFFSET :o'; // only an offset is not possible ???

		$sql = sprintf( 'SELECT %s FROM `%s`', $this->keys(), $this->name );
		$pre = cLen( $l ) ? $this->prepare( $sql ) : $this->query( $sql );

		if ( cLen( $l ) )
			$pre->bindParam( ':l', $limit, PDO::PARAM_INT );

		if ( cLen( $o ) )
			$pre->bindParam( ':o', $offset, PDO::PARAM_INT );

		if ( !$pre->execute() )
			throw new Error( sprintf( 'Could not get objects in "%s"', $this->name ) );

		return $pre->fetchAll( PDO::FETCH_CLASS );

	}

	// returns the pdo parameter corresponding to the field
	// maybe a little short :)
	protected function getPdoParam( string $k ) {
		return strpos( lower( explode( ' ', $this->fields[$k] )[0] ), 'int' ) > -1 ? PDO::PARAM_INT : PDO::PARAM_STR;
	}

	// gets the executed statement by a speciffic key
	public function getByKey( string $k, $d ) {

		// maybe should check if this key exists???
		// slows a little bit down
		if ( !isset( $this->fields[$k] ) )
			throw new Error( sprintf( 'In table "%s" key "%s" not found', $this->name, $k ) );

		$sql = sprintf( 'SELECT %s FROM `%s` WHERE `%s`=:v', $this->keys(), $this->name, $k );
		$pre = $this->prepare( $sql );

		$pre->bindParam( ':v', $d, $this->getPdoParam( $k ) );

		if ( !$pre->execute() )
			throw new Error( sprintf( 'Could not get %s"%s" in "%s"', $k, $d, $this->name ) );

		return $pre;

	}

	// returns one entry by id
	public function getById( int $id ) {

		$pre = $this->getByKey( $this->key(), $id );

		return $pre->fetchObject();

	}

	// inserts as many fields as we have (not id)
	// maybe should check the length ????
	public function insert() {

		$args = func_get_args();

		$ks = [];
		$keys = [];
		$vs = [];

		reset( $this->fields );

		for ( $i = 0; $i < count( $args ); $i++ ) {

			next( $this->fields ); // because we skip the id
			$k = key( $this->fields );
			$ks[] = $k;
			$keys[] = sprintf( '`%s`', $k );
			$vs[] = ':'. $k;

		}

		$sql = sprintf( 'INSERT INTO `%s` (%s) VALUES (%s)', $this->name, implode( ', ', $keys ), implode( ', ', $vs ) );
		$pre = $this->prepare( $sql );

		foreach ( $ks as $i => $k )
			$pre->bindParam( $vs[$i], $args[$i], $this->getPdoParam( $k ) );

		if ( !$pre->execute() )
			throw new Error( sprintf( 'Could not insert into "%s"', $this->name ) );

		return $this->pdo->lastInsertId();

	}

	// update data by id
	public function updateById( int $id, array $ar = [] ) {

		$d = [];

		foreach ( $ar as $k => $v ) {

			if ( !isset( $this->fields[$k] ) )
				throw new Error( sprintf( 'key "%s" in table "%s" not found', $k, $this->name ) );

			$d[] = sprintf( '`%s`=:%s', $k, $k );

		}

		$sql = sprintf( 'UPDATE `%s` SET %s WHERE `%s`=:id', $this->name, implode( ', ', $d ), $this->key() );
		$pre = $this->prepare( $sql );

		foreach ( $ar as $k => $v ) // array because bindparam uses references
			$pre->bindParam( ':'. $k, $ar[$k], $this->getPdoParam( $k ) );

		$pre->bindParam( ':id', $id, PDO::PARAM_INT );

		if ( !$pre->execute() )
			throw new Error( sprintf( 'Could not update into "%s"', $this->name ) );

		return true;

	}

	// deletes one entry by id
	public function deleteById( int $id ) {

		$sql = sprintf( 'DELETE FROM `%s` WHERE `%s`=:id', $this->name, $this->key() );
		$pre = $this->prepare( $sql );

		$pre->bindParam( ':id', $id, PDO::PARAM_INT );

		if ( !$pre->execute() )
			throw new Error( sprintf( 'Could not delete "%d" in "%s"', $id, $this->name ) );

	}

	// prepare an in statement
	protected function preIn( array $ar, string $prefix = 'in' ) {

		$ks = [];

		foreach ( $ar as $k => $v )
			$ks[] = ':'. $prefix. $k;

		return implode( ', ', $ks );

	}

	// binds the in parameters
	protected function in( &$pre, array $ar, $pdoType = null, string $prefix = 'in' ) {

		if ( isNil( $pdoType ) )
			$pdoType = PDO::PARAM_STR;

		foreach ( $ar as $k => $v )
			$pre->bindParam( ':'. $prefix. $k, $ar[$k], $pdoType );

	}

}