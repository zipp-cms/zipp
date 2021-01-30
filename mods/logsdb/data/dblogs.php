<?php
/*
@package: Zipp
@version: 0.2 <2019-07-12>
*/

namespace LogsDB\Data;

use Database\Table;
use \Error;
use \PDO;

class DbLogs extends Table {

	protected $_name = 'logs';

	protected $fields = [
			'logId' => 'INT UNSIGNED NOT NULL AUTO_INCREMENT',
			// or maybe varchar
			'cat' => 'VARCHAR(20) NOT NULL', // nulll if valid for every language
			'datetime' => 'DATETIME NOT NULL',
			'uri' => 'VARCHAR(200) NOT NULL',
			'exTime' => 'FLOAT NOT NULL',
			'mode' => 'VARCHAR(5) NOT NULL',
			'host' => 'VARCHAR(50) NOT NULL',
			'ip' => 'VARCHAR(39) NOT NULL',
			'referer' => 'VARCHAR(100) NOT NULL',
			'userAgent' => 'VARCHAR(200) NOT NULL',
			'customLog' => 'TEXT NOT NULL' // here are the different alt data stored
		];

	protected $indexes = 'PRIMARY KEY(`logId`), INDEX(`cat`), INDEX(`datetime`), INDEX(`uri`)';


	public function insertAll( array $list ) {

		$sql = sprintf( 'INSERT INTO `%s` (`cat`, `datetime`, `uri`, `exTime`, `mode`, `host`, `ip`, `referer`, `userAgent`, `customLog`) VALUES (:cat, :dat, :uri, :ex, :mode, :host, :ip, :referer, :uAgent, :customLog)', $this->name );
		$pre = $this->prepare( $sql );

		$cat = '';
		$dat = '';
		$uri = '';
		$ex = '';
		$mode = '';
		$host = '';
		$ip = '';
		$referer = '';
		$uAgent = '';
		$customLog = '';

		$pre->bindParam( ':cat', $cat, PDO::PARAM_STR );
		$pre->bindParam( ':dat', $dat, PDO::PARAM_STR );
		$pre->bindParam( ':uri', $uri, PDO::PARAM_STR );
		$pre->bindParam( ':ex', $ex, PDO::PARAM_STR );
		$pre->bindParam( ':mode', $mode, PDO::PARAM_STR );
		$pre->bindParam( ':host', $host, PDO::PARAM_STR );
		$pre->bindParam( ':ip', $ip, PDO::PARAM_STR );
		$pre->bindParam( ':referer', $referer, PDO::PARAM_STR );
		$pre->bindParam( ':uAgent', $uAgent, PDO::PARAM_STR );
		$pre->bindParam( ':customLog', $customLog, PDO::PARAM_STR );

		foreach ( $list as $l ) {
			$cat = $l[0];
			$dat = $l[1];
			$uri = $l[2];
			$ex = $l[3];
			$mode = $l[4];
			$host = $l[5];
			$ip = $l[6];
			$referer = $l[7];
			$uAgent = $l[8];
			$customLog = $l[9];
			if ( !$pre->execute() )
				throw new Error( 'Could not insert Log' );
		}

	}

	public function getAllLimit( int $limit ) {

		$sql = sprintf( 'SELECT %s FROM `%s` ORDER BY `datetime` DESC LIMIT %d', $this->keys(), $this->name, (int) ( $limit <= 0 ? 100 : $limit ) );
		$pre = $this->query( $sql );

		if ( !$pre->execute() )
			throw new Error( sprintf( 'Could not get media in "%s" with lang "%s"', $this->name, $lang ) );

		return $pre->fetchAll( PDO::FETCH_CLASS );

	}

	public function getShortInRange( string $cat, string $startDate, string $endDate ) {

		$sql = sprintf( 'SELECT `logId`, `datetime`, `uri`, `host` FROM `%s` WHERE `cat`=:cat AND `datetime` BETWEEN :endDate AND :startDate ORDER BY `datetime` DESC', $this->name );
		$pre = $this->prepare( $sql );

		$pre->bindParam( ':cat', $cat, PDO::PARAM_STR );
		$pre->bindParam( ':startDate', $startDate, PDO::PARAM_STR );
		$pre->bindParam( ':endDate', $endDate, PDO::PARAM_STR );

		if ( !$pre->execute() )
			throw new Error( sprintf( 'Could not get Logs (%s) in range (%s/%s)', $cat, $startDate, $endDate ) );

		return $pre->fetchAll( PDO::FETCH_CLASS );

	}

}