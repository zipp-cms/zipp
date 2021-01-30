<?php
/*
@package: Zipp
@version: 0.2 <2019-07-12>
*/

namespace LogsDB;

use Core\Module;
use Data\Module as DataModule;
use \Error;

class Logs extends Module {

	use DataModule;

	// PROPERTIES
	protected $handlers = [ 'database' => 'Data\DBLogs' ];

	public function getAll() {
		$this->import();
		return $this->handler->getAllLimit( 500 );
	}

	public function getShortReqForDays( int $days ) {

		$this->import();

		$nums = 0;
		$perDates = [];
		$perUri = [];

		$list = $this->getShortForDays( 'req', $days );
		$nums = count( $list );
		foreach ( $list as $entry ) {

			$date = substr( $entry->datetime, 0, 10 );
			if ( !isset( $perDates[$date] ) )
				$perDates[$date] = 0;
			$perDates[$date]++;

			$uri = $entry->uri;
			if ( !isset( $perUri[$uri] ) )
				$perUri[$uri] = 0;
			$perUri[$uri]++;

		}

		return [ $nums, $perDates, $perUri ];

	}

	// INIT
	public function onInstalling() {
		$this->handler->create();
	}

	// PROTECTED
	protected function import() {
		$list = $this->mods->Logs->getAll();
		if ( has( $list ) ) {
			$this->handler->insertAll( $list );
			$this->mods->Logs->clearAll();
		}
	}

	// days should be greater than 1
	public function getShortForDays( string $cat, int $days ) {

		$startDate = date('Y-m-d H:i:s');

		$endDate = date( 'Y-m-d H:i:s', strtotime( sprintf( '-%d day', $days ) ) );

		return $this->handler->getShortInRange( $cat, $startDate, $endDate );

	}

}