<?php
/*
@package: Zipp
@version: 0.1 <2019-06-13>
*/

namespace PagesInt\Pages;

use Ajax\Request as AjaxRequest;
use Admin\{Page, DataRequest};

class Pages extends Page {

	protected $section = 'pages';

	protected $slug = 'pages';

	protected $template = 'pages';

	public function onData( DataRequest $req ) {

		$s = $this->mods->SiteInt;
		$pages = $this->mods->Pages->getAll( $s->getCookieLang() );

		$nLayouts = $this->mods->Themes->layoutNames;

		$ps = [];

		foreach ( $pages as $p )
			$ps[] = $p->exportShort( $nLayouts );

		return [
			'title' => $this->lang->pagesTitle,
			'pages' => $ps,
			'editUrl' => $this->admin->bUrl( 'pages/edit/' )
		];

	}

}