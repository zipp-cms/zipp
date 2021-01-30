<?php
/*
@package: Zipp
@version: 0.2 <2019-07-01>
*/

namespace Pages;

use Core\Module;
use Data\Module as DataModule;
use Router\Module as RouterModule;
use \Error;

class Pages extends Module {

	use DataModule,
		RouterModule;

	protected $interfaces = [];

	protected $activePage = null;

	protected $handlers = [ 'database' => 'Data\DbPages' ];

	// GETTERS
	public function _getActivePage() {
		return $this->activePage;
	}

	// METHODS

	// CHECK
	public function hasUrl( string $url, string $lang ) {
		return $this->handler->hasUrl( $url, $lang );
	}

	// GET MANY
	public function getAll( string $lang, string $def = null ) {
		return $this->handler->getAllPages( $lang, $def );
	}

	public function getByLayouts( string $lang, array $layouts ) {
		return $this->handler->getAllByLayouts( $lang, $layouts );
	}

	public function allCtnIds( int $pId ) {
		return $this->handler->allCtnIds( $pId );
	}

	// filters by state
	public function getAllByCtnIds( array $ids ) {

		// should return pages

		return $this->handler->getAllByCtnIds( $ids );
	}

	// filters by state
	public function getTitlesByLayouts( array $layouts = null, string $lang ) {
		// layouts
		return $this->handler->getTitlesByLayouts( $layouts, $lang );
	}

	// filters by state
	public function getTitlesAndUrlByCtnIds( array $ids ) {
		return $this->handler->getTitlesAndUrlByCtnIds( $ids );
	}


	// GET ONE

	public function getById( int $pId, string $lang = null ) {
		return $this->handler->getAllById( $pId, $lang );
	}

	// filters by state
	public function getByUrl( string $url, string $lang ) {
		return $this->handler->getByUrl( $url, $lang );
	}

	// filters by state
	public function getTitlesAndUrlByCtnId( int $ctnId ) {
		return $this->handler->getTitlesAndUrlByCtnId( $ctnId );
	}

	// filters by state
	public function getAllByCtnId( int $ctnId ) {
		return $this->handler->getAllByCtnId( $ctnId );
	}


	// NEW (INSERT)

	public function newPage( string $layout, int $uId, string $lang ) {
		// $this->handler->new( $layout, $uId, now() );

		$pId = $this->handler->insertPage( $layout, $uId, now() );

		
		$this->handler->newContentNull( $pId, $lang );

		return $pId;

	}

	// 
	public function newPageLang( int $pId, string $lang ) {
		$this->handler->newContentNull( $pId, $lang );
	}

	public function newPageLangCopy( Page $page, string $lang ) {
		$this->handler->newContentCopy( $page, $lang );
	}


	// EDIT (UPDATE)

	// everyone has the right to update the content
	public function updateCtn( int $cId, string $url, string $title, array $ctn, array $keywords, int $state, string $publishOn ) {
		return $this->handler->updateCtn( $cId, $url, $title, $ctn, $keywords, $state, $publishOn );
	}

	public function changeState( int $cId, int $state ) {
		$this->handler->updateState( $cId, $state );
	}

	
	// DELETE

	public function delByPage( int $pId ) {
		$this->handler->delByPage( $pId );
	}

	public function delCtn( int $cId ) {
		$this->handler->delCtn( $cId );
	}


	// SPECIALS

	// filters by state
	public function executeQuery( array $ids = null, array $layouts = null, array $order = null, int $amount ) {
		$lang = $this->activePage->lang;
		return $this->handler->executeQuery( $ids, $layouts, $order, $amount, $lang );
	}

	public function install() {
		$this->handler->install();
	}


	public function isMl() {
		return (bool) $this->mods->Site->get( 'multilingual', false );
	}

	public function setActivePage( Page $page ) {
		$this->activePage = $page;
	}

	// INIT
	public function onInit() {
		$this->routerRegister( '/', 'RouterInteractor' );
	}

	public function onInstalling() {
		$this->install();
	}

}