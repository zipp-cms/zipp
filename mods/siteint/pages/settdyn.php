<?php
/*
@package: Zipp
@version: 0.1 <2019-06-17>
*/

namespace SiteInt\Pages;

use Ajax\Request as AjaxRequest;

use Admin\Page;
use Admin\DataRequest;

class SettDyn extends Page {

	protected $section = 'settings';

	protected $template = 'site';

	public function onData( DataRequest $req ) {

		// get slug
		$k = $req->parts[1];
		$this->slug = $k;

		// Validate Page
		$sett = $this->mods->Themes->settings[$k] ?? null;
		if ( isNil( $sett ) )
			return sprintf( 'Site "%s" settings not found', $k );

		// Check Languages
		$l = $this->lang;
		$lang = $this->mod->getCookieLang();

		// get all field slugs
		$ks = [];
		foreach ( $sett->fields as $field )
			$ks[] = $field->slug;

		// get all Fields data multilingual
		$d = $this->mods->Site->getAllMl( $ks, $lang );

		// export fields
		$fs = [];
		foreach ( $sett->fields as $field )
			$fs[] = $field->export( $d );

		// initialize Nonce
		$this->nonceKey = $k;

		return [
			'title' => $sett->name,
			'fields' => $fs,
			'nonce' => $this->nonce(),
			'key' => $k,
			'baselang' => $lang,
			'langsSelect' => $this->mod->langs,
			'multilingual' => $this->mod->multilingual
		];

	}

	public function onAjax( AjaxRequest $req ) {

		$d = $req->data;

		$k = $d->key ?? null;
		$lang = $d->baselang ?? null;

		if ( !is_string( $k ) || !is_string( $k ) )
			return $req->error( 'key or lang empty' );

		// should validate lang

		$this->nonceKey = $k;

		if ( !$this->checkNonce( $req ) )
			return;

		$sett = $this->mods->Themes->settings[$k] ?? null;
		if ( !is_object( $sett ) )
			return $req->error( 'Site settings not found' );

		$data = [];
		foreach ( $sett->fields as $field ) {

			if ( !$field->validate( $d ) )
				return $req->formError( sprintf( $this->lang->fieldError, $field->name ), $this->newNonce() );

			$data[$field->slug] = $field->out( $d );

		}

		$this->mods->Site->setMultMl( $data, $lang );

		// maybe should add this as events
		$this->mods->Themes->contentChanged( 'siteupdate', $data );

		$req->formOk( true, $this->newNonce() );

	}

}