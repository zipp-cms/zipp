<?php
/*
@package: Zipp
@version: 0.1 <2019-08-23>
*/

namespace LogsInt;

use Ajax\Interactor;
use Ajax\Request;
use Media\Media;

class AjaxInteractor extends Interactor {

	public function on( Request $req ) {

		if ( !$this->mods->Users->isLoggedIn() )
			return $req->error( $this->mod->lang->notLoggedIn );

		switch ( $req->event ) {

			case 'basic':
				$this->getBasic( $req );
				break;

			default:
				$req->error( 'no event found' );
				break;

		}

	}

	protected function getBasic( Request $req ) {

		$days = (int) ( $req->data->days ?? 0 );
		if ( $days <= 0 )
			return $req->error( 'no enough days' );

		$logs = $this->mods->LogsDB;

		$results = $logs->getShortReqForDays( $days );

		$total = $results[0];

		$perUri = [];
		foreach ( $results[2] as $uri => $count )
			$perUri[] = [$uri, $count];

		$l = $this->mod->lang;

		$req->ok( (object) [
			'title' => $l->title,
			'total' => $total,
			'perDates' => $results[1],
			'perUri' => $perUri
		] );

	}

}