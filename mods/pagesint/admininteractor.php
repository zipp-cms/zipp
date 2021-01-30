<?php
/*
@package: Zipp
@version: 0.1 <2019-06-02>
*/

namespace PagesInt;

use Admin\Interactor;

class AdminInteractor extends Interactor {

	protected function onInit() {

		$l = $this->lang;

		$this->addScripts( ['fields', 'popups', 'main'] );
		$this->addStyle( 'style', 'mgcss' );

		$this->addSection( 'pages', $l->pagesSection, -20 );

		$this->addPage( 'Pages\EditPage' );
		$this->addPageUrl( 'pages/edit/', 'editpage' );

		$this->addPage( 'Pages\NewPage' );
		$this->addPageUrl( 'pages/new/', 'newpage' );

		$this->addPage( 'Pages\NewPageLang' );
		$this->addPageUrl( 'pages/newlang/', 'newpagelang' );

		$this->addPage( 'Pages\DelPage' );
		$this->addPageUrl( 'pages/delpage/', 'delpage' );

		$this->addSingle( 'Pages\Pages', 'pages', $l->pagesTitle, 'all-pages' );

		// do the dynamic stuff
		foreach ( $this->mods->Themes->pageCats as $k => $cat ) {
			$this->addPage( $k, 'Pages\PagesDyn' );
			$this->addPageToSection( 'pages', $k, $cat->name, 'page-cat' );
		}

	}

}