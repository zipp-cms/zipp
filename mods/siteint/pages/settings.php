<?php
/*
@package: Zipp
@version: 0.1 <2019-06-18>
*/

namespace SiteInt\Pages;

use Ajax\Request as AjaxRequest;
use Admin\{Page, DataRequest};
use Fields\Fields\{Text, CheckBox, SingleRepeat, DropDown};

class Settings extends Page {

	protected $nonceKey = 'site-edit';

	protected $section = 'settings';

	protected $slug = 'settings';

	protected $template = 'site';

	public function onData( DataRequest $req ) {

		$l = $this->lang;
		$lang = $this->mod->getCookieLang();
		$s = $this->mods->Site;

		$d = (object) [
			'name' => $s->getMl( 'name', $lang ),
			'multilingual' => $this->mod->multilingual,
			'languages' => $this->mod->langs,
			'theme' => $s->get( 'theme' )
		];

		$fs = [];
		foreach ( $this->setupFields() as $f )
			$fs[] = $f->export( $d );

		return [
			'title' => $l->siteTitle,
			'fields' => $fs,
			'nonce' => $this->nonce(),
			'baselang' => $lang,
			'langsSelect' => $d->languages,
			'multilingual' => $d->multilingual,
			'url' => $this->admin->bUrl( 'settings/' )
		];

	}

	protected function setupFields() {

		$l = $this->lang;
		$lOpts = $this->mods->Langs->getAllPossible();

		return [
			new Text( 'name', [ 'name' => $l->titleField ] ),
			new CheckBox( 'multilingual', [ 'name' => $l->mlField ] ),
			new SingleRepeat( 'languages', [
				'addBtn' => $l->addLang,
				'removeBtn' => $l->removeLang,
				'field' => new DropDown( 'lang', [
					'name' => $l->languageField,
					'desc' => $l->languageFieldDesc,
					'options' => $lOpts
				] )
			] ),
			new Text( 'theme', [ 'name' => $l->titleTheme ] )
		];

	}

	public function onAjax( AjaxRequest $req ) {

		if ( !$this->checkNonce( $req ) )
			return;

		$data = $req->data;
		$lang = $data->baselang ?? null;

		// this error should not happen
		if ( !is_string( $lang ) )
			return $req->error( 'Please define a lang!' );

		// validate lang
		$lang = $this->mod->getValidatedLang( $lang );

		// Validate Fields
		$fields = $this->setupFields();
		$d = [];

		foreach ( $fields as $field ) {

			if ( !$field->validate( $data ) )
				return $req->formError( sprintf( 'error with field %s', $field->name ), $this->newNonce() );

			$d[$field->slug] = $field->out( $data );

		}

		$d = (object) $d;

		// save data
		$s = $this->mods->Site;
		$s->setMl( 'name', $lang, $d->name );
		$s->set( 'multilingual', $d->multilingual );
		$s->set( 'languages', $d->languages );
		$s->set( 'theme', $d->theme );

		// maybe should add this as events
		$this->mods->Themes->contentChanged( 'siteupdate', $d );

		$req->formOk( true, $this->newNonce() );

	}

}