<?php
/*
@package: Zipp
@version: 0.1 <2019-06-11>
*/

namespace Media\Data;

use Database\Table;
use \Error;
use \PDO;

class DbMedia extends Table {

	protected $_name = 'media';

	protected $fields = [
			'mediaId' => 'INT UNSIGNED NOT NULL AUTO_INCREMENT',
			// or maybe varchar
			'lang' => 'CHAR(5) NOT NULL', // nulll if valid for every language
			'name' => 'VARCHAR(30) NOT NULL',
			'type' => 'VARCHAR(10) NOT NULL',
			'size' => 'INT NOT NULL',
			'content' => 'VARCHAR(255) NOT NULL', // here are the different alt data stored
			'createdOn' => 'DATETIME NOT NULL'
		];

	protected $indexes = 'PRIMARY KEY(`mediaId`), INDEX(`lang`), UNIQUE(`name`, `type`)';

	public function getAllByLang( string $lang ) {

		$sql = sprintf( 'SELECT %s FROM `%s` WHERE `lang`="nulll" OR `lang`=:l', $this->keys(), $this->name );
		$pre = $this->prepare( $sql );

		$pre->bindParam( ':l', $lang, PDO::PARAM_STR );

		if ( !$pre->execute() )
			throw new Error( sprintf( 'Could not get media in "%s" with lang "%s"', $this->name, $lang ) );

		return $pre->fetchAll( PDO::FETCH_CLASS );

	}

	public function update( int $id, string $lang, string $ctn ) {
		$this->updateById( $id, [
			'lang' => $lang,
			'content' => $ctn
		] );
	}

	public function getByIds( array $ids ) {

		$sql = sprintf( 'SELECT %s FROM %s WHERE `mediaId` IN (%s)', $this->keys(), $this->name, $this->preIn( $ids ) );
		$pre = $this->prepare( $sql );

		$this->in( $pre, $ids, PDO::PARAM_INT );

		if ( !$pre->execute() )
			throw new Error( sprintf( 'Could not get media by ids (%s)', implode( ', ', $ids ) ) );

		return $pre->fetchAll( PDO::FETCH_CLASS );

	}


}