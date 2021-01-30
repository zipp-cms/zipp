<?php
/*
@package: Zipp
@version: 1.0 <2021-01-27>
*/

namespace Pages\Data;

use Database\Table;
use \Error;
use \PDO;

class PagesTable extends Table {

	protected $_name = 'pages';

	protected $fields = [
			'pageId' => 'INT UNSIGNED NOT NULL AUTO_INCREMENT',
			'layout' => 'VARCHAR(30) NOT NULL',
			'createdBy' => 'INT UNSIGNED NOT NULL',
			'createdOn' => 'DATETIME NOT NULL',
		/*	'lockedBy' => 'INT UNSIGNED NOT NULL AUTO_INCREMENT',
			'lockedOn' => 'DATETIME NOT NULL'*/
		];
	/*
	dbPages
	- layout > defines cache and theme
	- theme??
	- created??
	*/

	protected $indexes = 'PRIMARY KEY(`pageId`), INDEX(`layout`)';

	// CHECK
	public function idExists( int $id ) {

		$sql = sprintf( 'SELECT COUNT(`pageId`) as count FROM %s WHERE `pageId`=:i', $this->name );
		$pre = $this->prepare( $sql );

		$pre->bindParam( ':i', $id, PDO::PARAM_INT );

		if ( !$pre->execute() )
			throw new Error( sprintf( 'Could not count pages by id "%d"', $id ) );

		return $pre->fetchObject()->count > 0;

	}

	// GET MANY
	public function getByLayouts( array $layouts ) {

		$sql = sprintf( 'SELECT %s FROM %s WHERE `layout` IN (%s) ORDER BY `pageId` ASC', $this->keys(), $this->name, $this->preIn( $layouts ) );
		$pre = $this->prepare( $sql );

		$this->in( $pre, $layouts );

		if ( !$pre->execute() )
			throw new Error( sprintf( 'Could not get pages by layouts (%s)', implode( ', ', $layouts ) ) );

		return $pre->fetchAll( PDO::FETCH_CLASS );

	}

	public function getByIds( array $ids ) {

		$sql = sprintf( 'SELECT %s FROM %s WHERE `pageId` IN (%s)', $this->keys(), $this->name, $this->preIn( $ids ) );
		$pre = $this->prepare( $sql );

		$this->in( $pre, $ids, PDO::PARAM_INT );

		if ( !$pre->execute() )
			throw new Error( sprintf( 'Could not get pages by ids (%s)', implode( ', ', $ids ) ) );

		return $pre->fetchAll( PDO::FETCH_CLASS );

	}

	// GET ONE
	public function getById( int $id ) {

		$sql = sprintf( 'SELECT %s FROM %s WHERE `pageId`=:id', $this->keys(), $this->name );
		$pre = $this->prepare( $sql );

		$pre->bindParam( ':id', $id, PDO::PARAM_INT );

		if ( !$pre->execute() )
			throw new Error( sprintf( 'Could not get page by id %d', $id ) );

		return $pre->fetchObject();

	}


	// SPECIAL
	public function executeQuery( array $ids = null, array $layouts = null, array $orders = null ) {

		$list = [];

		if ( !isNil( $ids ) || !isNil( $layouts ) )
			$list[] = 'WHERE';

		if ( !isNil( $ids ) )
			$list[] = sprintf( '`pageId` IN (%s)', $this->preIn( $ids, 'in_ids' ) );

		if ( !isNil( $ids ) && !isNil( $layouts ) )
			$list[] = 'AND';

		if ( !isNil( $layouts ) )
			$list[] = sprintf( '`layout` IN (%s)', $this->preIn( $layouts, 'in_lys' ) );

		if ( !isNil( $orders ) ) {
			$asc = $orders[0];
			$key = $orders[1];
			if ( isset( $this->fields[$key] ) )
				$list[] = sprintf( 'ORDER BY `%s` %s', $key, $asc ? 'ASC' : 'DESC' );
		}

		$sql = sprintf( 'SELECT %s FROM %s %s', $this->keys(), $this->name, implode( ' ', $list ) );
		$pre = $this->prepare( $sql );

		if ( !isNil( $ids ) )
			$this->in( $pre, $ids, PDO::PARAM_INT, 'in_ids' );

		if ( !isNil( $layouts ) )
			$this->in( $pre, $layouts, PDO::PARAM_STR, 'in_lys' );

		if ( !$pre->execute() )
			throw new Error( 'Could not get pages with query' );

		return $pre->fetchAll( PDO::FETCH_CLASS );

	}


}