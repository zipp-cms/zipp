<?php
/*
@package: Zipp
@version: 0.1 <2019-05-31>
*/

namespace Pages;

class Page {

	// page
	protected $id = 0;

	protected $layout = '';

	protected $createdBy = 0;

	protected $createdOn = '2019-05-31 09:31:00';

	protected $langs = [];

	// ctn
	protected $ctnId = 0;

	protected $lang = '';

	protected $url = '';

	protected $title = '';

	protected $ctn = null;

	protected $keywords = [];

	protected $state = 0;

	protected $publishOn = '2019-05-31 09:31:00';

	protected $completedUrl = false;

	public function __construct( object $page, array $ctns, string $lang = null ) {

		$this->id = (int) $page->pageId;
		$this->layout = $page->layout;
		$this->createdBy = (int) $page->createdBy;
		$this->createdOn = $page->createdOn;

		$ctn = null;
		$lngs = [];

		foreach ( $ctns as $lng => $ct ) {

			if ( isNil( $ctn ) || $lng === $lang )
				$ctn = $ct;

			$lngs[$lng] = (object) [
				'url' => $ct->url,
				'title' => $ct->title,
				'state' => $ct->state,
				'lang' => $lng
			];

		}

		$this->langs = $lngs;

		$this->ctnId = (int) $ctn->ctnId;
		$this->lang = $ctn->lang;
		$this->url = $ctn->url ?? '';
		$this->title = $ctn->title;
		$this->ctn = json_decode( $ctn->ctn );
		$this->keywords = explode( ',', $ctn->keywords );
		$this->state = (int) $ctn->state;
		$this->publishOn = $ctn->publishOn;

	}

	public function exportShort( array $layouts ) {

		// convert langs

		return [
			'id' => $this->id,
			'layout' => $layouts[$this->layout] ?? $this->layout,
			'langs' => array_values( $this->langs ),
			'lang' => $this->lang,
			'title' => $this->title,
			'state' => $this->state,
			'keywords' => $this->keywords
		];

	}

	public function completeUrl( string $url, bool $withLang = false ) {

		if ( $this->completedUrl )
			return;
		$this->completedUrl = true;

		if ( $withLang )
			$this->url = cleanUrl( sprintf( '%s%s/%s', $url, $this->lang, $this->url ) );
		else
			$this->url = cleanUrl( $url. $this->url );
		// this reference isnt necessary
		foreach ( $this->langs as $k => &$page )
			$page->url = cleanUrl( sprintf( '%s%s/%s', $url, $k, $page->url ) );

	}

	public function replaceCtn( object $ctn ) {
		$this->ctn = $ctn;
	}

	public function __get( string $k ) {
		
		if ( isset( $this->$k ) )
			return $this->$k;

		return $this->ctn->$k ?? null;
	}

	public function __debugInfo() {
		return [
			'id' => $this->id,
			'layout' => $this->layout,
			'createdBy' => $this->createdBy,
			'createdOn' => $this->createdOn,
			'langs' => $this->langs,
			'ctnId' => $this->ctnId,
			'lang' => $this->lang,
			'url' => $this->url,
			'title' => $this->title,
			'ctn' => '*',
			'keywords' => $this->keywords,
			'state' => $this->state,
			'publishOn' => $this->publishOn,
			'*' => array_keys( (array) $this->ctn )
		];
	}

}