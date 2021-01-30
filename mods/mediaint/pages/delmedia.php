<?php
/*
@package: Zipp
@version: 0.1 <2019-06-25>
*/

namespace MediaInt\Pages;

use Ajax\Request as AjaxRequest;
use Admin\{Page, DataRequest};
use \Error;

class DelMedia extends Page {

	protected $nonceKey = 'delmedia';

	public function onData( DataRequest $req ) {

		$mediaId = (int) ( $req->parts[2] ?? 0 );

		if ( $mediaId <= 0 )
			return 'Id is incorrect';

		return [
			'nonce' => $this->nonce(),
			'mediaId' => $mediaId
		];

	}

	public function onAjax( AjaxRequest $req ) {

		if ( !$this->checkNonce( $req ) )
			return;

		$l = $this->lang;
		$d = $req->data;

		$mediaId = (int) ( $d->id ?? 0 );

		if ( $mediaId <= 0 )
			return $req->error( $l->delError );

		$media = $this->mods->Media;

		$item = $media->getById( $mediaId );

		if ( !$item )
			return $req->error( $l->delError );

		$path = $media->getPath(). $item->getFilename();

		@unlink( $path );
		$media->delete( $mediaId );

		$req->ok( $this->admin->bUrl( 'media/' ) );

	}

}