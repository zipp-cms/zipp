<?php
/*
@package: Zipp
@version: 0.1 <2019-05-28>
*/

namespace Admin;

use Core\Module;
use Core\MagicGet;

class MainInteractor extends Interactor {

	protected function onInit() {

		$l = $this->lang;

		$this->baseAddScript();
		$this->baseAddStyle();

		// add login, here because it should be available without login
		$this->addPage( 'Pages\Login' );

		if ( !$this->isLoggedIn() )
			return;

		$this->addSection( 'home', $l->homeSection, -10 );

		$list = [
			[ 'Pages\Home', 'home', $l->homeTitle, 'dashboard' ]
		];

		if ( $this->isAdmin() ) {
			$this->addSection( 'dev', $l->devSection, -100 );
			$list[] = [ 'Pages\Dev', 'dev', $l->devTitle, 'development' ];
			$list[] = [ 'Pages\DevModules', 'dev', $l->devModulesTitle, 'development' ];
			$list[] = [ 'Pages\DevPack', 'dev', $l->devPackTitle, 'development' ];
		}

		$this->addMultiple( $list );

	}

	protected function baseAddScript() {

		$this->addScript( 'helper' );

		// extern Files
		$this->admin->addScripts( 'fields', $this->mods->Fields->scripts );
		$this->admin->addScript( 'ajax', $this->mods->Ajax->scriptFile );
		$this->admin->addScript( 'time', $this->mods->Time->scriptFile );

		$this->addScripts([
			'forms/form', 'forms/ajaxforms',
			'cookies', 'popup', 'pages', 'docevents',
			'zipp',
			'pages/login', 'pages/home', 'pages/dev', 'pages/devmodules', 'pages/devpack'
		]);

	}

	protected function baseAddStyle() {

		$this->addStyle( 'main', 'mgcss' );

		$fieldsStyleFile = $this->mods->Fields->styleFile;
		$this->admin->addStyle( 'fields', $fieldsStyleFile[0], $fieldsStyleFile[1] );
		$timeStyleFile = $this->mods->Time->styleFile;
		$this->admin->addStyle( 'time', $timeStyleFile[0], $timeStyleFile[1] );

	}

}