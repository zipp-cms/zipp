<?php
/*
@package: Zipp
@version: 0.1 <2019-06-11>
*/

namespace MediaInt\Pages;

use Ajax\Request as AjaxRequest;
use Admin\{Page, DataRequest};

use Fields\Fields\{DropDown, Text};
use \Error;

class MediaEdit extends Page {

	protected $section = 'media';

	protected $slug = 'mediaedit';

	protected $nonceKey = 'mediaedit';

	protected $template = 'edit';
	
	public function onData( DataRequest $req ) {

		$id = (int) ( $req->parts[2] ?? 0 );
		if ( $id <= 0 )
			return 'id incorrect';

		$itm = $this->mods->Media->getById( $id );
		if ( !$itm )
			return 'Media item not found';

		$fields = [];
		foreach ( $this->getFields() as $f )
			$fields[] = $f->export( $itm );

		return [
			'title' => $this->lang->editTitle,
			'fields' => $fields,
			'item' => $itm->exportShort(),
			'nonce' => $this->nonce(),
			'langs' => $this->mods->SiteInt->langs
		];

	}

	protected function getFields() {

		$l = $this->lang;
		$sInt = $this->mods->SiteInt;
		$langs = $sInt->niceLangs;
		$options = array_merge( [ 'nulll' => $l->everyLang ], $langs );
		$fs = [
			new Text( 'name', [
				'name' => $l->nameField
			] ),
			
		];

		// only show lang drop if in multilingual
		if ( $sInt->multilingual ) {

			// add lang dropdown
			$fs[] = new DropDown( 'lang', [
				'name' => $l->langField,
				'options' => $options
			] );

			foreach ( $langs as $k => $lang )
				$fs[] = new Text( 'alt'. $k, [ 'name' => sprintf( $l->altField, $lang ) ] );

		} else {

			foreach ( $langs as $k => $lang ) {
				$fs[] = new Text( 'alt'. $k, [ 'name' => sprintf( $l->altField, $lang ) ] );
				break;
			}

		}

		return $fs;

	}

	public function onAjax( AjaxRequest $req ) {

		if ( !$this->checkNonce( $req ) )
			return;

		$l = $this->lang;
		$d = $req->data;

		$id = (int) ( $d->id ?? 0 );

		if ( $id <= 0 )
			throw new Error( 'id incorrect' );

		// should validate lang more

		$fields = $this->getFields();
		$data = [];

		foreach ( $fields as $f ) {
			if ( !$f->validate( $d ) )
				return $req->error( sprintf( $l->fieldError, $f->name ) );
			$data[$f->slug] = $f->out( $d );
		}

		$sInt = $this->mods->SiteInt;
		if ( $sInt->multilingual ) {
			$lang = $data['lang'];
			unset( $data['lang'] );
		} else
			$lang = 'nulll';
		

		$name = $data['name'];
		unset( $data['name'] );

		$this->mods->Media->edit( $id, $lang, $data );

		$req->formOk( true, $this->newNonce() );

	}

}