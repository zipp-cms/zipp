<?php
/*
@package: Zipp
@version: 0.1 <2019-06-21>
*/

namespace PagesInt\Pages;

use Ajax\Request;
use Admin\{Page, DataRequest};
use \Error;

class DelPage extends Page {

	protected $nonceKey = 'delpage';

	public function onData( DataRequest $req ) {

		$pageId = (int) ( $req->parts[2] ?? 0 );
		$ctnId = (int) ( $req->parts[3] ?? 0 );

		if ( $pageId <= 0 || $ctnId <= 0 )
			return 'Id is incorrect';

		return [
			'nonce' => $this->nonce(),
			'pageId' => $pageId,
			'ctnId' => $ctnId
		];

	}

	public function onAjax( Request $req ) {

		if ( !$this->checkNonce( $req ) )
			return;

		$l = $this->lang;
		$d = $req->data;
		$types = [ 'archive', 'delete' ];

		$pageId = (int) ( $d->pageId ?? 0 );
		$ctnId = (int) ( $d->ctnId ?? 0 );
		$type = (string) ( $d->type ?? '' );

		if ( $pageId <= 0 || $ctnId <= 0 || !in_array( $type, $types ) )
			return $req->error( $l->delError ); // i could throw an error to

		$p = $this->mods->Pages;

		$ids = $p->allCtnIds( $pageId );
		if ( !has( $ids ) || !in_array( $ctnId, $ids ) )
			return $req->error( $l->delError );

		switch ( $type ) {

			case 'archive':
				$p->changeState( $ctnId, 0 );
				break;

			case 'delete':

				if ( count( $ids ) > 1 )
					$p->delCtn( $ctnId );
				else
					$p->delByPage( $pageId );
				break;

		}

		// maybe should add this as events
		$this->mods->Themes->contentChanged( 'pagedelete', (object) [
			'pageId' => $pageId,
			'ctnId' => $ctnId,
			'type' => $type
		] );

		if ( count( $ids ) === 1 )
			return $req->ok( $this->admin->bUrl( 'pages/' ) );
		
		$req->ok( 'reload' );

	}

}