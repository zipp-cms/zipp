<?php
/*
@package: Zipp
@version: 1.0 <2019-06-17>
*/

namespace SiteInt;

use Admin\Interactor;

class AdminInteractor extends Interactor {

	protected function onInit() {

		$l = $this->lang;
		// $u = $this->users;

		$this->addSection( 'settings', $l->settingsSection, -40 );

		// ah users should be initialized now
		$this->addStyle( 'style', 'mgcss' );
		$this->addScript( 'main' );

		// activate the cli interactor
		$this->addSingle( 'Pages\Settings', 'settings', $l->siteTitle, 'settings' );

		foreach ( $this->mods->Themes->settings as $k => $sett ) {
			$this->addPage( $k, 'Pages\SettDyn' );
			$this->addPageToSection( 'settings', $k, $sett->name, 'settings' );
		}

	}

}