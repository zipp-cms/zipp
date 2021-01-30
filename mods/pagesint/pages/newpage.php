<?php
/*
@package: Zipp
@version: 0.1 <2019-05-28>
*/

namespace PagesInt\Pages;

use Ajax\Request as AjaxRequest;
use Admin\{Page, DataRequest};
use Fields\Fields\DropDown;

class NewPage extends Page {

	protected $nonceKey = 'newpage';

	public function onData( DataRequest $req ) {

		$fields = [];
		$d = (object) [];

		foreach ( $this->fields() as $f )
			$fields[] = $f->export( $d );

		return [
			'fields' => $fields,
			'nonce' => $this->nonce()
		];

	}

	protected function fields() {

		$layouts = $this->mods->Themes->layoutNames;
		$sInt = $this->mods->SiteInt;

		$l = $this->lang;

		$fields = [
			new DropDown( 'layout', [
				'name' => $l->layoutField,
				'options' => $layouts
			] )
		];

		// only show lang dropdown if multilingual is selected
		if ( $sInt->multilingual ) {

			$langs = $this->mods->SiteInt->niceLangs;

			$fields[] = new DropDown( 'lang', [
					'name' => $l->langField,
					'options' => $langs
				] );
		}

		return $fields;
	}

	public function onAjax( AjaxRequest $req ) {

		if ( !$this->checkNonce( $req ) )
			return;

		$fields = $this->fields();
		$sInt = $this->mods->SiteInt;
		$l = $this->lang;

		$d = [];

		foreach ( $fields as $f ) {

			if ( !$f->validate( $req->data ) )
				return $req->formError( sprintf( $l->newPageError, $f->name ), $this->newNonce() );

			$d[$f->slug] = $f->out( $req->data );

		}

		// add lang attribute if not selectable
		if ( !$sInt->multilingual ) {
			// TODO is this the right to get the default lang???
			$d['lang'] = $sInt->langs[0];
		}

		$d = (object) $d;

		$uId = $this->mods->Users->id;
		$p = $this->mods->Pages->newPage( $d->layout, $uId, $d->lang );

		// maybe should add this as events
		$this->mods->Themes->contentChanged( 'pagenew', $d );

		if ( $p )
			return $req->ok( $this->admin->bUrl( sprintf( 'pages/edit/%d/', $p ) ) );

		$req->formError( $l->errorNewPage, $this->newNonce() );

	}

}