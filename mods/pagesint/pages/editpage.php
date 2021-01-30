<?php
/*
@package: Zipp
@version: 0.1 <2019-05-28>
*/

namespace PagesInt\Pages;

use Ajax\Request as AjaxRequest;
use Admin\{Page, DataRequest};

use Fields\Fields\{Text, DropDown, Hidden, Keywords};
use PagesInt\Fields\PageUrl;
use Time\Fields\Time;

use \Error;
use Pages\Page as PagesPage;

class EditPage extends Page {

	protected $section = 'pages';

	protected $slug = 'editpage';

	protected $nonceKey = 'editpage'; // maybe should add the page-id at the end
	// at the moment your not allowed to have two pages at the same time

	protected $template = 'edit';

	public function onData( DataRequest $req ) {

		if ( !has( $req->parts, 3 ) )
			return $this->error();

		$pageId = (int) $req->parts[2];
		$sInt = $this->mods->SiteInt;
		$lang = $sInt->getRawCookieLang(); // will always return 

		if ( $pageId <= 0 )
			return $this->error();

		// this will return any existing page if the language isnt available
		$p = $this->mods->Pages->getById( $pageId, $lang );

		if ( !$p )
			return $this->error();

		// get components and fields
		$comps = $this->mods->Themes->getFieldsByLayout( $p->layout );
		foreach ( $comps as &$c ) {

			$fd = [];
			foreach ( $c->fields as $f )
				$fd[] = $f->export( $p );

			$c->fields = $fd;

		}

		// export main fields
		$mf = [];
		foreach ( $this->mainFields() as $f )
			$mf[] = $f->export( $p );

		// could create new lang
		$cCNL = count( $sInt->langs ) > count( $p->langs );

		return [
			'title' => $this->lang->editPageTitle,
			'comps' => $comps,
			'mainF' => $mf,
			'nonce' => $this->nonce(),
			'langsSelect' => array_keys( $p->langs ),
			'multilingual' => $sInt->multilingual,
			'baselang' => $p->lang,
			'couldCreateNewLang' => $cCNL
			/*'langDrop' => $langDrop,
			'newLangDrop' => $newLangDrop,
			'previewLink' => $previewLink,
			'delData' => $delData,
			'newPageLangNonce' => $n->newForm( 'newpagelang' )*/
		];

	}

	protected function error() {
		return $this->lang->pageIdUndefined;
	}

	public function onAjax( AjaxRequest $req ) {

		if ( !$this->checkNonce( $req ) )
			return;

		$l = $this->lang;
		$d = $req->data;

		// lang errors should not occur (so throw)
		if ( !is_string( $d->lang ?? null ) )
			throw new Error( 'Lang is undefined!' );

		$lang = $this->mods->SiteInt->getValidatedLang( $d->lang );

		// Main Field Validation
		$mFields = $this->mainFields();
		$data = [];

		foreach ( $mFields as $f ) {

			if ( !$f->validate( $d ) )
				return $req->formError( sprintf( $l->editFieldError, $f->name ), $this->newNonce() );

			$data[$f->slug] = $f->out( $d );

		}

		$data = (object) $data;

		// Theme Field Validation
		$t = $this->mods->Themes;
		$comps = $t->getFieldsByLayout( $data->layout );
		// this layout field is not "secure"
		// i dont see a problem in, that layout could be changed

		$ctn = [];
		
		foreach ( $comps as $c ) {

			foreach ( $c->fields as $f ) {

				if ( !$f->validate( $d ) )
					return $req->formError( sprintf( $l->editFieldError, $f->name ), $this->newNonce() );

				$ctn[$f->slug] = $f->out( $d );

			}

		}

		// Page Stuff
		$p = $this->mods->Pages;

		$dbCtnId = $p->hasUrl( $data->url, $lang );
		if ( $dbCtnId && $dbCtnId !== $data->ctnId )
			return $req->formError( $l->editPageUrlExists, $this->newNonce() );

		// you can create an sql error if you modify lang > so you get a collision
		// that isnt a problem because the database has a unique

		$p->updateCtn( $data->ctnId, $data->url, $data->title, $ctn, $data->keywords, $data->state, $data->publishOn );

		// this says the theme hey ive save a new version of the page
		// maybe you need to remove the cache?
		// $t->updatedPage( $data->url, $lang, $data->layout );
		$t->contentChanged( 'pageupdate', $data );

		// now we need to parse the fields

		$req->formOk( true, $this->newNonce() );

	}

	protected function mainFields() {

		$l = $this->lang;

		$stateOpt = [
			'0' => $l->stateArchive,
			'1' => $l->statePreview,
			'2' => $l->stateLive
		];

		return [
			new Hidden( 'id', [ 'sett' => [ 'type' => 'int', 'min' => 1 ] ] ),
			new Hidden( 'ctnId', [ 'sett' => [ 'type' => 'int', 'min' => 1 ] ] ),
			new Hidden( 'layout', [ 'sett' => [ 'req' => true ] ] ), // used to get fields of the layout
			new Hidden( 'lang', [ 'sett' => [ 'req' => true ] ] ), // lang for pageURL check
			new Text( 'title', [
				'name' => $l->titleField,
				'sett' => [ 'req' => true, 'max' => 50 ]
			] ),
			new PageUrl( 'url', [ 'name' => $l->urlField ] ),
			new Keywords( 'keywords', [ 'name' => $l->keywordsField ] ),
			new DropDown( 'state', [
				'name' => $l->stateField, 
				'options' => $stateOpt
			] ),
			new Time( 'publishOn', [ 'name' => $l->publishField ] )
		];

	}

}