<?php
/*
@package: Zipp
@version: 1.0 <2019-06-17>
*/

namespace Pages\Data;

use Database\Table;
use \PDO;

class ContentTable extends Table {

	protected $_name = 'pagescontent';

	protected $fields = [
			'ctnId' => 'INT UNSIGNED NOT NULL AUTO_INCREMENT',
			'pageId' => 'INT UNSIGNED NOT NULL',
			'lang' => 'CHAR(5) NOT NULL',
			'url' => 'VARCHAR(50) NULL',
			'title' => 'VARCHAR(50) NOT NULL',
			'ctn' => 'TEXT NOT NULL',
			'keywords' => 'VARCHAR(255) NOT NULL',
			'state' => 'TINYINT UNSIGNED NOT NULL', // why do i need this?
			'publishOn' => 'DATETIME NOT NULL'/*,
			'deactivateOn' => 'DATETIME NOT NULL'
			cannot use this since when cached there is no way this gets checked :/ (maybe cron???)
			*/
		];

	// state 0: archive 1:preview 2:published

	protected $indexes = 'PRIMARY KEY(`ctnId`), INDEX(`pageId`), UNIQUE(`url`, `lang`), INDEX(`keywords`), INDEX(`state`), INDEX(`publishOn`)';

	// HELPERS
	protected function showOnlyLive() {
		return !defined( 'PAGES_SHOW_PREVIEW' );
	}

	protected function stateSql() {

		$stateSql = '=2';
		if ( !$this->showOnlyLive() )
			$stateSql = ' IN (1,2)';

		return 'AND `state`'. $stateSql;

	}

	// CHECK
	public function hasUrl( string $url, string $lang ) {
		
		$sql = sprintf( 'SELECT `ctnId` FROM %s WHERE `url`=:u AND `lang`=:l LIMIT 1', $this->name );
		$pre = $this->prepare( $sql );

		$pre->bindParam( ':u', $url, PDO::PARAM_STR );
		$pre->bindParam( ':l', $lang, PDO::PARAM_STR );

		if ( !$pre->execute() )
			throw new Error( sprintf( 'Could not count content by url "%s" and lang "%s"', $url, $lang ) );

		$obj = $pre->fetchObject();
		return $obj ? (int) $obj->ctnId : false;

	}

	public function langExists( int $pId, string $lang ) {

		$sql = sprintf( 'SELECT COUNT(`ctnId`) as count FROM %s WHERE `pageId`=:i AND `lang`=:l', $this->name );
		$pre = $this->prepare( $sql );

		$pre->bindParam( ':i', $pId, PDO::PARAM_INT );
		$pre->bindParam( ':l', $lang, PDO::PARAM_STR );

		if ( !$pre->execute() )
			throw new Error( sprintf( 'Could not count content by pageId "%d" and lang "%s"', $pId, $lang ) );

		return $pre->fetchObject()->count > 0;

	}

	// GET MANY (SELECT)
	// doesnt filter by state
	public function getAllSorted() {

		$sql = sprintf( 'SELECT %s FROM `%s` ORDER BY `pageId` ASC', $this->keys(), $this->name );
		$pre = $this->query( $sql );

		if ( !$pre->execute() )
			throw new Error( 'Could not get all content sorted by pageId' );

		return $pre->fetchAll( PDO::FETCH_CLASS );

	}

	public function getAllById( int $id, bool $filterState = false ) {

		if ( !$filterState ) {
			$pre = $this->getByKey( 'pageId', $id );
			return $pre->fetchAll( PDO::FETCH_CLASS );
		}

		// if should filter state
		$sql = sprintf( 'SELECT %s FROM `%s` WHERE `pageId`=:id %s', $this->keys(), $this->name, $this->stateSql() );
		$pre = $this->prepare( $sql );

		$pre->bindParam( ':id', $id, PDO::PARAM_INT );

		if ( !$pre->execute() )
			throw new Error( 'Could not get all by page Id' );

		return $pre->fetchAll( PDO::FETCH_CLASS );

	}

	public function allIdsByPage( int $pId ) {

		$sql = sprintf( 'SELECT `ctnId` FROM %s WHERE `pageId`=:i', $this->name );
		$pre = $this->prepare( $sql );

		$pre->bindParam( ':i', $pId, PDO::PARAM_INT );

		if ( !$pre->execute() )
			throw new Error( sprintf( 'Could not count content by pageId "%d"', $pId ) );

		$ids = [];
		foreach ( $pre->fetchAll( PDO::FETCH_CLASS ) as $ctn )
			$ids[] = $ctn->ctnId;

		return $ids;

	}

	// can be filtered by state
	public function getByIds( array $ids, bool $filterByState = false ) {

		$stateSql = $filterByState ? $this->stateSql() : '';
		$sql = sprintf( 'SELECT %s FROM %s WHERE `ctnId` IN (%s) %s', $this->keys(), $this->name, $this->preIn( $ids ), $stateSql );
		$pre = $this->prepare( $sql );

		$this->in( $pre, $ids, PDO::PARAM_INT );

		if ( !$pre->execute() )
			throw new Error( sprintf( 'Could not get contents by ids (%s)', implode( ', ', $ids ) ) );

		return $pre->fetchAll( PDO::FETCH_CLASS );

	}

	public function getByPageIds( array $ids ) {

		$sql = sprintf( 'SELECT %s FROM %s WHERE `pageId` IN (%s) ORDER BY `pageId` ASC', $this->keys(), $this->name, $this->preIn( $ids ) );
		$pre = $this->prepare( $sql );

		$this->in( $pre, $ids, PDO::PARAM_INT );

		if ( !$pre->execute() )
			throw new Error( sprintf( 'Could not get contents by page ids (%s)', implode( ', ', $ids ) ) );

		return $pre->fetchAll( PDO::FETCH_CLASS );

	}

	// filters by state
	public function getShortByPageIdsAndLang( array $ids, string $lang ) {

		$sql = sprintf( 'SELECT `ctnId`, `pageId`, `title`, `url`, `lang` FROM %s WHERE `pageId` IN (%s) AND `lang`=:lang %s', $this->name, $this->preIn( $ids ), $this->stateSql() );
		$pre = $this->prepare( $sql );

		$this->in( $pre, $ids, PDO::PARAM_INT );
		$pre->bindParam( ':lang', $lang, PDO::PARAM_STR );

		if ( !$pre->execute() )
			throw new Error( sprintf( 'Could not get contents by page ids (%s) and lang (%s)', implode( ', ', $ids ), $lang ) );

		return $pre->fetchAll( PDO::FETCH_CLASS );

	}

	// filters by state
	public function getShortByIds( array $ids ) {

		$sql = sprintf( 'SELECT `ctnId`, `pageId`, `title`, `url`, `lang` FROM %s WHERE `ctnId` IN (%s) %s', $this->name, $this->preIn( $ids ), $this->stateSql() );
		$pre = $this->prepare( $sql );

		$this->in( $pre, $ids, PDO::PARAM_INT );

		if ( !$pre->execute() )
			throw new Error( sprintf( 'Could not get contents by ids (%s)', implode( ', ', $ids ) ) );

		return $pre->fetchAll( PDO::FETCH_CLASS );

	}

	// filters by state
	public function executeQuery( array $ids, array $orders = null, int $amount, bool &$doesOrder ) {

		$list = [];

		if ( !isNil( $orders ) ) {
			$asc = $orders[0];
			$key = $orders[1];
			if ( isset( $this->fields[$key] ) ) {
				$doesOrder = true;
				$list[] = sprintf( 'ORDER BY `%s` %s', $key, $asc ? 'ASC' : 'DESC' );
			}
		}

		if ( $amount > 0 )
			$list[] = sprintf( 'LIMIT %d', $amount );

		// Show only Live Pages
		$sql = sprintf( 'SELECT %s FROM %s WHERE `pageId` IN (%s) %s %s', $this->keys(), $this->name, $this->preIn( $ids ), $this->stateSql(), implode( ' ', $list ) );

		$pre = $this->prepare( $sql );

		$this->in( $pre, $ids, PDO::PARAM_INT );

		if ( !$pre->execute() )
			throw new Error( 'Could not get contents by query' );

		return $pre->fetchAll( PDO::FETCH_CLASS );

	}

	// GET ONE (SELECT)

	// can be filtered by state
	public function getById( int $id, bool $filterByState = false ) {

		$stateSql = $filterByState ? $this->stateSql() : '';

		$sql = sprintf( 'SELECT %s FROM `%s` WHERE `ctnId`=:id %s LIMIT 1', $this->keys(), $this->name, $stateSql );
		$pre = $this->prepare( $sql );

		$pre->bindParam( ':id', $id, PDO::PARAM_INT );

		if ( !$pre->execute() )
			throw new Error( sprintf( 'Could not get content by id "%d"', $id ) );

		return $pre->fetchObject();

	}

	// filter by state
	public function getByUrl( string $url, string $lang ) {

		$sql = sprintf( 'SELECT `pageId` FROM `%s` WHERE `url`=:u AND `lang`=:l %s LIMIT 1', $this->name, $this->stateSql() );
		$pre = $this->prepare( $sql );

		$pre->bindParam( ':u', $url, PDO::PARAM_STR );
		$pre->bindParam( ':l', $lang, PDO::PARAM_STR );

		if ( !$pre->execute() )
			throw new Error( sprintf( 'Could not get content by url "%s"', $url ) );

		return $pre->fetchObject();

	}

	public function getByPageId( int $page, string $lang ) {

		$sql = sprintf( 'SELECT %s FROM `%s` WHERE `pageId`=:p AND `lang`=:l LIMIT 1', $this->keys(), $this->name );
		$pre = $this->prepare( $sql );

		$pre->bindParam( ':p', $page, PDO::PARAM_INT );
		$pre->bindParam( ':l', $lang, PDO::PARAM_STR );

		if ( !$pre->execute() )
			throw new Error( sprintf( 'Could not get content by page id "%d" and lang "%s"', $page, $lang ) );

		return $pre->fetchObject();

	}

	// filters by state
	public function getShortById( int $id ) {

		$sql = sprintf( 'SELECT `ctnId`, `pageId`, `title`, `url`, `lang` FROM %s WHERE `ctnId`=:id %s LIMIT 1', $this->name, $this->stateSql() );
		$pre = $this->prepare( $sql );

		$pre->bindParam( ':id', $id, PDO::PARAM_INT );

		if ( !$pre->execute() )
			throw new Error( sprintf( 'Could not get content by id (%d)', $id ) );

		return $pre->fetchObject();

	}

	// NEW (INSERT)
	public function newNull( int $pageId, string $lang ) {
		
		$sql = sprintf( 'INSERT INTO %s (`pageId`, `lang`, `url`, `title`, `ctn`, `keywords`, `state`, `publishOn`) VALUES (:pId, :l, NULL, "", "\"\"", "", 1, :d)', $this->name );

		$pre = $this->prepare( $sql );

		$pre->bindParam( ':pId', $pageId, PDO::PARAM_INT );
		$pre->bindParam( ':l', $lang, PDO::PARAM_STR );
		$v = now();
		$pre->bindParam( ':d', $v, PDO::PARAM_STR );

		if ( !$pre->execute() )
			throw new Error( sprintf( 'Could not insert content with pageId "%d" and lang "%s"', $page, $lang ) );

		return $this->pdo->lastInsertId();

	}

	// EDIT (UPDATE)
	public function updateCtn( int $cId, string $url, string $title, string $ctn, string $keywords, int $state, string $publishOn ) {
		return $this->updateById( $cId, [
			'url' => $url,
			'title' => $title,
			'ctn' => $ctn,
			'keywords' => $keywords,
			'state' => $state,
			'publishOn' => $publishOn
		] );
	}

	public function updateState( int $cId, int $state ) {
		$this->updateById( $cId, [
			'state' => $state
		] );
	}


	// DELETE
	public function delByPage( int $pId ) {

		$sql = sprintf( 'DELETE FROM `%s` WHERE `pageId`=:id', $this->name );
		$pre = $this->prepare( $sql );

		$pre->bindParam( ':id', $pId, PDO::PARAM_INT );

		if ( !$pre->execute() )
			throw new Error( sprintf( 'Could not delete "%d" in "%s"', $id, $this->name ) );

	}

}