<?php
/*
@package: Zipp
@version: 1.0 <2019-05-28>
*/

namespace PagesInt\Pages;

use Ajax\Request as AjaxRequest;
use Admin\{Page, DataRequest};

class PagesDyn extends Page {

	protected $section = 'pages';

	protected $template = 'pages';

	public function onData( DataRequest $req ) {

		$k = $req->parts[1];
		$this->slug = $k;

		$cat = $this->mods->Themes->pageCats[$k] ?? null;

		if ( isNil( $cat ) )
			return 'Page category not found';

		$s = $this->mods->SiteInt;
		$pages = $this->mods->Pages->getByLayouts( $s->getDefaultLang(), $cat->layouts );

		$nLayouts = $this->mods->Themes->layoutNames;

		$ps = [];

		foreach ( $pages as $p )
			$ps[] = $p->exportShort( $nLayouts );

		return [
			'title' => $cat->name,
			'pages' => $ps,
			'editUrl' => $this->admin->bUrl( 'pages/edit/' )
		];

	}

}