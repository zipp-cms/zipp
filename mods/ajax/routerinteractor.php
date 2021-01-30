<?php
/*
@package: Zipp
@version: 0.1 <2019-05-28>
*/

namespace Ajax;

use Router\Interactor;
use Router\Request as RouterRequest;
use \Exception;

class RouterInteractor extends Interactor {

	public function on( RouterRequest $rReq ) {

		$parts = $rReq->parts;

		$req = new Request( $this->mod, $rReq );

		if ( count( $parts ) !== 2 )
			return $req->error( 'parts required' );

		$int = $this->mod->getInt( $parts[0] );

		if ( !$int )
			return $req->error( sprintf( 'mod %s not registered in ajax', $parts[0] ) );

		$req->event = $parts[1];

		try {

			$int->on( $req );

		} catch ( Exception $e ) {
			$req->error( $e->getMessage() );
		}

	}

}