<?php
/*
@package: Zipp
@version: 0.2 <2019-07-04>
*/

namespace Pages;

use Themes\Theme;

class PageQuery {

	protected $pages = null;

	protected $theme = null;

	protected $layouts = null;

	protected $ids = null;

	// null if nothing gets ordered else
	// [ascending|bool, key|str, sortedInDb|bool]
	// last value gets set when the database determines
	// if it can sort it or not
	protected $order = null;

	protected $amount = -1;

	public function __construct( Pages $pages, Theme $theme ) {
		$this->pages = $pages;
		$this->theme = $theme;
	}

	public function byLayouts( array $layouts ) {
		$this->layouts = $layouts;
		return $this;
	}

	public function byLayout( string $layout ) {
		$this->layouts = [$layout];
		return $this;
	}

	public function byIds( array $ids ) {
		$this->ids = $ids;
		return $this;
	}

	public function byId( int $id ) {
		$this->ids = [$id];
		return $this;
	}

	public function orderDesc( string $key ) {
		$this->order = [false, $key, false];
		return $this;
	}

	public function orderAsc( string $key ) {
		$this->order = [true, $key, false];
		return $this;
	}

	public function limit( int $amount ) {
		$this->amount = $amount;
		return $this;
	}

	public function all() {
		$this->amount = -1;
		return $this;
	}

	public function one() {
		$this->amount = 1;
		return $this;
	}

	public function full() {

		$pages = $this->go();

		foreach ( $pages as $p ) {
			$ly = $this->theme->resolvePage( $p );
			$p->replaceCtn( $ly->fullFill( $p ) );
		}

		$this->maybeSort( $pages );

		return $this->filterAmount( $pages );
	}

	public function short() {

		$pages = $this->go();

		foreach ( $pages as $p )
			$this->theme->completeUrlOnPage( $p );

		$this->maybeSort( $pages );

		return $this->filterAmount( $pages );
	}

	public function query() {

		$pages = $this->go();

		foreach ( $pages as $p ) {
			$ly = $this->theme->resolvePage( $p );
			$p->replaceCtn( $ly->fillFields( $p ) );
		}

		$this->maybeSort( $pages );

		return $this->filterAmount( $pages );
	}

	protected function go() {
		return $this->pages->executeQuery( $this->ids, $this->layouts, $this->order, $this->amount );
	}

	protected function filterAmount( array $pages ) {
		return $this->amount === 1 ? ( $pages[0] ?? null ) : $pages;
	}

	protected function maybeSort( array &$pages ) {
		if ( isNil( $this->order ) || $this->order[2] )
			return;

		// do some sorting
	}

	// full

	// short

	// query

}