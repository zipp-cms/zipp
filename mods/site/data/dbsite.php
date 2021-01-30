<?php
/*
@package: Zipp
@version: 0.1 <2019-05-28>
*/

namespace Site\Data;

use Database\Table;
use \PDO;
use \Error;

class DbSite extends Table {

	protected $_name = 'site';

	protected $fields = [
			'key' => 'VARCHAR(30) NOT NULL',
			'lang' => 'CHAR(5)',
			'value' => 'TEXT NOT NULL'
		];

	protected $indexes = 'PRIMARY KEY(`key`, `lang`)';


	public function getAllByLang( string $lang ) {

		$sql = sprintf( 'SELECT %s FROM `%s` WHERE `lang`=:l', $this->keys(), $this->name );
		$pre = $this->prepare( $sql );

		$pre->bindParam( ':l', $lang, PDO::PARAM_STR );

		if ( !$pre->execute() )
			throw new Error( sprintf( 'Could not get data with lang "%s"', $lang ) );

		$d = [];

		foreach ( $pre->fetchAll( PDO::FETCH_CLASS ) as $fd )
			$d[$fd->key] = $fd->value;

		return $d;

	}

	public function getAllByLangNull() {
		return $this->getAllByLang( 'nulll' );
	}

	public function set( string $k, string $value ) {
		$this->setMl( $k, 'nulll', $value );
	}

	public function setMl( string $k, string $lang, string $value ) {

		$sql = sprintf( 'INSERT INTO `%s` (`key`, `lang`, `value`) VALUES (:k, :l, :v1) ON DUPLICATE KEY UPDATE `value`=:v2', $this->name );
		$pre = $this->prepare( $sql );

		$pre->bindParam( ':k', $k, PDO::PARAM_STR );
		$pre->bindParam( ':l', $lang, PDO::PARAM_STR );
		$pre->bindParam( ':v1', $value, PDO::PARAM_STR );
		$pre->bindParam( ':v2', $value, PDO::PARAM_STR );

		if ( !$pre->execute() )
			throw new Error( sprintf( 'Could not set data "%s" with lang "%s"', $k, $lang ) );

	}

	public function setMultMl( array $data, string $lang ) {

		$sql = sprintf( 'INSERT INTO `%s` (`key`, `lang`, `value`) VALUES (:k, :l, :v1) ON DUPLICATE KEY UPDATE `value`=:v2', $this->name );
		$pre = $this->prepare( $sql );

		$pre->bindParam( ':l', $lang, PDO::PARAM_STR );

		$k = '';
		$v = '';
		$pre->bindParam( ':k', $k, PDO::PARAM_STR );
		$pre->bindParam( ':v1', $v, PDO::PARAM_STR );
		$pre->bindParam( ':v2', $v, PDO::PARAM_STR );

		foreach ( $data as $k => $v )
			if ( !$pre->execute() )
				throw new Error( sprintf( 'Could not set data "%s" with lang "%s"', $k, $lang ) );

	}

}