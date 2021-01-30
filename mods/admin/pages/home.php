<?php
/*
@package: Zipp
@version: 1.0 <2019-05-28>
*/

namespace Admin\Pages;

use Admin\{Page, DataRequest};
use Router\Interactor;
use Router\Request;
use Ajax\Request as AjaxRequest;

class Home extends Page {

	protected $section = 'home';

	protected $slug = 'home';

	protected $template = 'home';

	public function onData( DataRequest $req ) {

		if ( $req->uri !== '/' && $req->uri !== 'home/' )
			return $this->lang->pageNotFound;
		// error 404

		return [
			'title' => $this->lang->homeTitle
		];

	}

}