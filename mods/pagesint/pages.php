<?php
/*
@package: Zipp
@version: 0.2 <2019-07-02>
*/

namespace PagesInt;

use Core\{Module, KERNEL};
use Langs\Module as LangsModule;
use Fields\Module as FieldsModule;
use \Error;

class Pages extends Module {

	use LangsModule, FieldsModule;

	public function onInit() {

		if ( $this->mods->has( 'CLI' ) )
			$this->cli = new SetupCLI( $this );

		if ( $this->mods->has( 'Admin' ) )
			$this->admin = new SetupAdmin( $this );

		$this->addFields( ['Navigation', 'PageUrl', 'Page'] );

		if ( $this->mods->has( 'Users' ) ) {

			$sess = $this->mods->Session;
			$sKey = 'CACHE_SKIP';
			if ( $this->mods->Users->isLoggedIn() ) {

				if ( !$sess->get( $sKey, false ) )
					$sess->set( $sKey, true );

				define( 'PAGES_SHOW_PREVIEW', true );
				define( 'CACHE_DONT_SAVE', true );

			} else if ( $sess->get( $sKey, false ) )
				$sess->delete( $sKey );

		}

	}

	public static function getTitlesByLayoutsCookieLang( array $layouts = null ) {

		$pages = KERNEL::getInstance( 'Pages' );
		$lang = $pages->mods->SiteInt->getCookieLang(); // this is to display in admin

		return $pages->getTitlesByLayouts( $layouts, $lang );

	}

	public static function getNavigationPagesByIds( array $ids = [] ) {

		$pages = KERNEL::getInstance( 'Pages' );

		$ctns = $pages->getTitlesAndUrlByCtnIds( $ids );

		// fill with url
		$isMl = $pages->mods->SiteInt->multilingual;
		foreach ( $ctns as &$ctn ) {
			$langUri = $isMl ? $ctn->lang. '/' : '';
			$ctn->url = cleanUrl( $pages->mods->Router->url( $langUri. $ctn->url ) );
			$ctn->active = (int) $ctn->ctnId === $pages->activePage->ctnId;
		}

		return $ctns;

	}

	public static function getFieldPageById( int $id ) {

		$pages = KERNEL::getInstance( 'Pages' );

		$ctn = $pages->getTitlesAndUrlByCtnId( $id );

		if ( isNil( $ctn ) )
			return null;

		// fill with url
		$isMl = $pages->mods->SiteInt->multilingual;
		$router = $pages->mods->Router;
		$langUri = $isMl ? $ctn->lang. '/' : '';
		$ctn->url = cleanUrl( $router->url( $langUri. $ctn->url ) );

		return $ctn;
	}

	public static function getNavigationFullPagesByIds( array $ids = [] ) {

		$pages = KERNEL::getInstance( 'Pages' );
		$themes = $pages->mods->Themes;

		// $ctns = $pages->getTitlesAndUrlByCtnIds( $ids, $lang );
		$pgs = $pages->getAllByCtnIds( $ids );

		// fill with url
		$ctns = [];
		foreach ( $pgs as $page ) {
			$ly = $themes->resolvePage( $page );

			$ctn = $ly->fullFill( $page );

			$ctns[$page->ctnId] = (object) [
				'pageId' => $page->id,
				'title' => $page->title,
				'url' => $page->url,
				'lang' => $page->lang,
				'layout' => $page->layout,
				'ctn' => $ctn,
				'active' => $page->ctnId === $pages->activePage->ctnId
			];

		}

		return $ctns;

	}

	public static function getFieldPageFullById( int $id ) {

		$pages = KERNEL::getInstance( 'Pages' );
		$themes = $pages->mods->Themes;

		$page = $pages->getAllByCtnId( $id );

		if ( isNil( $page ) )
			return null;

		$ly = $themes->resolvePage( $page );
		$ctn = $ly->fullFill( $page );

		return (object) [
			'pageId' => $page->id,
			'title' => $page->title,
			'url' => $page->url,
			'lang' => $page->lang,
			'layout' => $page->layout,
			'ctn' => $ctn
		];

	}

}