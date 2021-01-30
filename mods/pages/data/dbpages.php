<?php
/*
@package: Zipp
@version: 0.2 <2019-07-01>
*/

namespace Pages\Data;

use Core\MagicGet;
use Core\Modules;
use Pages\Page;
use \Error;

class DbPages extends MagicGet {

	protected $mods = null;

	protected $bPages = null;

	protected $bCtn = null;

	// GETTERS
	public function _getPages() {

		if ( isNil( $this->bPages ) )
			$this->bPages = new PagesTable( $this->mods );

		return $this->bPages;

	}

	public function _getCtn() {

		if ( isNil( $this->bCtn ) )
			$this->bCtn = new ContentTable( $this->mods );

		return $this->bCtn;

	}

	// METHODS

	// CHECK
	public function hasUrl( string $url, string $lang ) {
		return $this->ctn->hasUrl( $url, $lang );
	}

	public function idExists( int $id ) {
		return $this->pages->idExists( $id );
	}

	public function langExists( int $pId, string $lang ) {
		return $this->ctn->langExists( $pId, $lang );
	}

	// GETTERS ALL

	// filters by state
	public function getByUrl( string $url, string $lang ) {

		$ctn = $this->ctn->getByUrl( $url, $lang );
		if ( !$ctn )
			return $ctn;

		return $this->getAllById( $ctn->pageId, $lang, true );

	}

	// if def !== null will always return a page
	public function getAllPages( string $lang, string $def = null ) {

		$ps = $this->pages->getAll(); // could make maybe a yield????
		$ctns = $this->ctn->getAllSorted();
		$pages = [];

		foreach ( $ps as $p ) {

			// $ps is ascending
			$id = $p->pageId;

			$cts = [];

			foreach ( $ctns as $c ) {

				if ( $c->pageId !== $id )
					break;

				$cts[$c->lang] = array_shift( $ctns );

			}

			if ( !has( $cts ) )
				throw new Error( sprintf( 'no content for page %d found', $id ) );

			$l = isset( $cts[$lang] ) ? $lang : $def;

			$pages[] = new Page( $p, $cts, $l );

		}

		return $pages;

	}

	public function getAllByLayouts( string $lang, array $layouts ) {

		$ps = $this->pages->getByLayouts( $layouts );

		if ( !has( $ps ) )
			return [];

		$nPs = [];

		foreach ( $ps as $p )
			$nPs[$p->pageId] = $p;

		$ctns = $this->ctn->getByPageIds( array_keys( $nPs ) );
		$pages = [];

		foreach ( $nPs as $p ) {

			// $nPs is ascending
			$id = $p->pageId;

			$cts = [];

			foreach ( $ctns as $c ) {

				if ( $c->pageId !== $id )
					break;

				$cts[$c->lang] = array_shift( $ctns );

			}

			if ( !has( $cts ) )
				throw new Error( sprintf( 'no content for page %d found', $id ) );

			$pages[] = new Page( $p, $cts, $lang );

		}

		return $pages;

	}

	public function allCtnIds( int $pId ) {
		return $this->ctn->allIdsByPage( $pId );
	}

	// filters by state
	public function getTitlesByLayouts( array $layouts = null, string $lang ) {

		$pages = isNil( $layouts ) ? $this->pages->getAll() : $this->pages->getByLayouts( $layouts );

		$ids = [];
		foreach ( $pages as $p )
			$ids[] = (int) $p->pageId;

		if ( !has( $ids ) )
			return [];

		$ctns = $this->ctn->getShortByPageIdsAndLang( $ids, $lang );

		$pages = [];
		foreach ( $ctns as $ctn )
			$pages[(int) $ctn->ctnId] = $ctn->title;

		return $pages;

	}

	// filters by state
	public function getTitlesAndUrlByCtnIds( array $ids ) {
		
		$ctns = [];
		foreach ( $this->ctn->getShortByIds( $ids ) as $ctn )
			$ctns[(int) $ctn->ctnId] = $ctn;

		return $ctns;

	}

	// filters by state
	public function getAllByCtnIds( array $ids ) {
		
		$ctns = [];
		foreach ( $this->ctn->getByIds( $ids, true ) as $ctn )
			$ctns[(int) $ctn->pageId] = $ctn;

		$pages = [];
		foreach ( $this->pages->getByIds( array_keys( $ctns ) ) as $page ) {

			// new Page
			$ctn = $ctns[(int) $page->pageId];
			$pages[] = new Page( $page, [
				$ctn->lang => $ctn
			], $ctn->lang );

		}

		return $pages;
	}

	// GET ONE
	public function getAllById( int $id, string $lang = null, bool $filterState = false ) {

		$p = $this->pages->getById( $id );

		if ( !$p )
			return false;

		$ctns = $this->ctn->getAllById( $id, $filterState );

		$cts = [];

		foreach ( $ctns as $c ) {

			// why this????
			/*if ( (int) $c->pageId !== $id )
				break;*/

			$cts[$c->lang] = array_shift( $ctns );

		}

		if ( !has( $cts ) )
			throw new Error( sprintf( 'no content for page %d found', $id ) );

		return new Page( $p, $cts, $lang );

	}

	// filters by state
	public function getTitlesAndUrlByCtnId( int $id ) {
		return $this->ctn->getShortById( $id );
	}

	// filters by state
	public function getAllByCtnId( int $id ) {

		$ctn = $this->ctn->getById( $ids, true );

		if ( isNil( $ctn ) )
			return null;

		$page = $this->pages->getById( (int) $ctn->pageId );

		return new Page( $page, [
			$ctn->lang => $ctn
		], $ctn->lang );

	}

	// NEW (INSERT)
	public function newContentNull( int $pageId, string $lang ) {
		return $this->ctn->newNull( $pageId, $lang );
	}

	public function newContentCopy( Page $page, string $lang ) {

		$url = $page->url;

		// we check
		if ( $this->hasUrl( $url, $lang ) )
			$url .= lower( randomToken(3) ). '/';

		return $this->ctn->insert( $page->id, $lang, $url, $page->title, json_encode( $page->ctn ), implode( ',', $page->keywords ), 1, $page->publishOn );
	}

	public function insertPage( string $layout, int $uId, string $time ) {
		return $this->pages->insert( $layout, $uId, $time );
	}

	// EDIT (UPDATE)
	public function updateCtn( int $cId, string $url, string $title, array $ctn, array $keywords, int $state, string $publishOn ) {
		return $this->ctn->updateCtn( $cId, $url, $title, json_encode( $ctn ), implode( ',', $keywords ), $state, $publishOn );
	}

	public function updateState( int $cId, int $state ) {
		$this->ctn->updateState( $cId, $state );
	}

	// DELETE
	public function delByPage( int $pId ) {

		$this->pages->deleteById( $pId );
		$this->ctn->delByPage( $pId );

	}

	public function delCtn( int $cId ) {
		$this->ctn->deleteById( $cId );
	}


	// SPECIAL

	// filters by state
	public function executeQuery( array $ids = null, array $layouts = null, array $order = null, int $amount, string $lang ) {

		$ps = $this->pages->executeQuery( $ids, $layouts, $order );
		if ( !has( $ps ) )
			return [];

		$pages = [];
		foreach ( $ps as $p ) {
			$p->ctns = [];
			$pages[(int) $p->pageId] = $p;
		}

		$doesOrder = false;
		$ctns = $this->ctn->executeQuery( array_keys( $pages ), $order, $amount, $doesOrder );
		if ( !has( $ctns ) )
			return [];

		foreach ( $ctns as $ctn )
			$pages[(int) $ctn->pageId]->ctns[$ctn->lang] = $ctn;

		if ( $doesOrder ) {
			$nPages = [];
			foreach ( $ctns as $ctn )
				if ( $ctn->lang === $lang )
					$nPages[(int) $ctn->pageId] = $pages[(int) $ctn->pageId];
			$pages = $nPages;
		}

		$nPages = [];
		foreach ( $pages as $id => $p )
			if ( isset( $p->ctns[$lang] ) )
				$nPages[] = new Page( $p, $p->ctns, $lang );

		return $nPages;

	}

	
	public function install() {
		$this->ctn->create();
		$this->pages->create();
	}

	public function uninstall() {
		$this->ctn->drop();
		$this->pages->drop();
	}

	// INIT
	public function __construct( Modules $mods ) {
		$this->mods = $mods;
	}

}