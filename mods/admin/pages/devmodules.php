<?php
/*
@package: Zipp
@version: 0.1 <2019-06-14>
*/

namespace Admin\Pages;

use Admin\{Page, DataRequest};
use Router\Interactor;
use Router\Request;
use Ajax\Request as AjaxRequest;

class DevModules extends Page {

	protected $section = 'dev';

	protected $slug = 'devmodules';

	protected $template = 'dev';

	public function onData( DataRequest $req ) {

		if ( !$this->isAdmin() )
			return $l->authError;

		// $r = $this->mods->Router;

		$cfgs = $this->mods->getConfigs();

		return [
			'title' => $this->lang->devModulesTitle,
			'cfgs' => $cfgs
		];

	}

}