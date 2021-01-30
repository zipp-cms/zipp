<?php
/*
@package: Zipp
@version: 0.1 <2019-06-11>
*/

namespace MediaInt\Pages;

use Ajax\Request as AjaxRequest;
use Admin\{Page, DataRequest};

class Media extends Page {

	protected $section = 'media';

	protected $slug = 'media';

	protected $template = 'media';

	public function onData( DataRequest $req ) {

		$l = $this->lang;

		$sInt = $this->mods->SiteInt;
		$lang = $sInt->getCookieLang();

		$itms = [];

		foreach ( $this->mods->Media->getAll( $lang ) as $itm )
			$itms[] = $itm->exportShort();

		return [
			'title' => $l->mediaTitle,
			'items' => $itms,
			'editUrl' => $this->admin->bUrl( 'media/edit' ),
			'langsSelect' => $sInt->langs,
			'baselang' => $lang,
			'multilingual' => $sInt->multilingual
		];

	}

}