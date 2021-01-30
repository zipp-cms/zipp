<?php
/*
@package: Zipp
@version: 0.2 <2019-07-12>
*/

namespace LogsInt;

use Admin\Interactor;

class AdminInteractor extends Interactor {

	protected function onInit() {

		$l = $this->lang;

		$this->addScripts( ['chart', 'main'] );

		$this->addStyle( 'style', 'mgcss' );
		$this->addStyle( 'chart' );

		// home

		$this->addSingle( 'Pages\Logs', 'home', $l->logsTitle );

	}

}