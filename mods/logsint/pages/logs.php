<?php
/*
@package: Zipp
@version: 0.2 <2019-07-12>
*/

namespace LogsInt\Pages;

use Ajax\Request as AjaxRequest;
use Admin\{Page, DataRequest};

class Logs extends Page {

	protected $section = 'home';

	protected $slug = 'logs';

	protected $template = 'logs';

	public function onData( DataRequest $req ) {

		$l = $this->lang;

		// $sInt = $this->mods->SiteInt;
		// $lang = $sInt->getCookieLang();

		// $itms = [];

		// foreach ( $this->mods->Media->getAll( $lang ) as $itm )
		// 	$itms[] = $itm->exportShort();

		// $logs = $this->mods->Logs->getAll();
		$db = $this->mods->LogsDB;
		$logs = $db->getAll();
		// $mostRated = $this->mods->LogsDB->getMostRated();

		return [
			'title' => $l->logsTitle,
			'logs' => $logs,
			'rated' => []
		];

	}

}