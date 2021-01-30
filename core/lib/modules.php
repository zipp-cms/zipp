<?php
/*
@package: Zipp
@version: 0.2 <2019-07-05>
*/

namespace Core;

use \Error;

class Modules {

	protected $path = '';

	protected $stages = [];

	protected $cfgs = [];

	protected $classes = [];

	protected $maxStage = 0;

	protected $activeStage = -1;

	// METHODS
	// get a mod
	public function get( string $k ) {

		$k = lower( $k );

		if ( isset( $this->classes[$k] ) )
			return $this->classes[$k];

		return $this->loadMod( $k );

	}

	// check if modules has the module $k
	public function has( string $k ) {
		return isset( $this->cfgs[lower( $k )] );
	}

	// get modules configs
	public function getConfigs() {
		return $this->cfgs;
	}

	public function nextStage() {
		$this->activeStage++;
		return $this->activeStage <= $this->maxStage;
	}

	public function triggerEvent( string $event, bool $onStage = true ) {

		$list = $onStage ? $this->stages[$this->activeStage] ?? [] : array_keys( $this->cfgs );

		$meth = 'on'. ucfirst( $event );

		foreach ( $list as $k )
			if ( in_array( $event, $this->cfgs[$k]->events ) )
				$this->get( $k )->$meth();

	}

	// CONSTRUCT
	public function __construct() {

		$this->path = DIR. 'mods'. DS;

		Autoloader::addPath( $this->path );

		$this->loadStages();

	}


	// PROTECTED
	protected function loadStages() {

		$f = $this->path. 'stages.json';

		if ( is_file( $f ) ) {
			$d = FS::readJson( $f );
			$this->maxStage = $d[0];
			$this->cfgs = (array) $d[1];
			$this->stages = (array) $d[2];
			return;
		}

		$cfgs = [];
		$maxStage = 0;

		// no finished dependecy tree is available
		foreach ( $this->loadAvailable() as $n => $iniFile ) {

			$cfgs[$n] = $this->readInit( $iniFile );

			if ( $cfgs[$n]->stage > $maxStage )
				$maxStage = $cfgs[$n]->stage;

		}

		$this->maxStage = $maxStage;
		$this->cleanNotMandDep( $cfgs );

		// build the stages
		$this->cfgs = $cfgs;
		$this->stages = $this->buildStages( $cfgs );

		FS::writeJson( $f, [$this->maxStage, $this->cfgs, $this->stages] );

	}

	// list every module in the folder and saves it into the variable available
	protected function loadAvailable() {

		$avs = [];
		foreach ( FS::ls( $this->path, false ) as $dir )
			$avs[$dir] = $this->path. $dir. DS;

		return $avs;

	}

	// read init.cfg file in mods
	protected function readInit( string $path ) {

		$ctn = FS::checkAndRead( $path. 'init.cfg' );

		$parts = preg_split( '/\s?\n\r?\s?/', $ctn );
		$props = [
			'dependencies' => '',
			'stage' => '5',
			'version' => '1.0',
			'build' => '1',
			'events' => '',
			'extensions' => ''
		];

		foreach ( $parts as $p ) {

			if ( !cLen( $p ) || $p[0] === '#' || strpos( $p, ':' ) === false )
				continue;

			$ps = explode( ':', $p );
			$props[trim( $ps[0] )] = trim( $ps[1] );

		}

		$props = (object) $props;

		if ( !isset( $props->namespace ) || !isset( $props->class ) )
			throw new Error( sprintf( 'Init file %s needs at least <class> and <namespace>', $path. 'init.cfg' ) );

		// now parse props
		$props->dependencies = keywords( $props->dependencies );
		$props->events = keywords( $props->events );
		$props->stage = uInt( $props->stage );
		$props->extensions = keywords( $props->extensions );
		$props->build = (int) $props->build;

		// maybe should move this block
		foreach ( $props->extensions as $ext )
			KERNEL::checkExt( $ext );

		return $props;

	}

	// clean not mandatory /^\?/ dependencies which dont exist
	protected function cleanNotMandDep( array &$list ) {

		foreach ( $list as $n => &$cfg ) {

			$nD = [];

			foreach ( $cfg->dependencies as $dep ) {

				if ( $dep[0] === '?' ) {

					$dep = substr( $dep, 1 );
					if ( !isset( $list[$dep] ) )
						continue;

				}

				$nD[] = $dep;

			}

			$cfg->dependencies = $nD;

		}

	}

	protected function buildStages( array $list ) {

		$used = [];
		$stages = [];

		$this->depLoop( 0, $stages, $used, $list );

		if ( has( $list ) ) {
			var_dump( $list );
			throw new Error( 'could not resolve all dependencies' );
		}

		return $stages;

	}

	protected function depLoop( int $stage, array &$stages, array &$used, array &$list ) {

		$changed = false;
		$ar = $stages[$stage] ?? [];

		foreach ( $list as $n => $cfg ) {

			if ( $cfg->stage !== $stage )
				continue;

			foreach ( $cfg->dependencies as $dep )
				if ( !isset( $used[$dep] ) )
					continue 2;

			$used[$n] = '';
			$ar[] = $n;
			unset( $list[$n] );
			$changed = true;

		}

		if ( !$changed )
			$stage++;
		else
			$stages[$stage] = $ar;

		if ( $stage > $this->maxStage )
			return;

		$this->depLoop( $stage, $stages, $used, $list );

	}

	// load a module (require)
	protected function loadMod( string $mod ) {

		$mPath = $this->path. $mod. DS;

		if ( !isset( $this->cfgs[$mod] ) )
			throw new Error( sprintf( 'could not find module %s', $mod ) );

		$cfg = $this->cfgs[$mod];
		$cls = $cfg->namespace. '\\'. $cfg->class;

		$this->classes[$mod] = new $cls( $mPath, $mod, $cfg->namespace, $this );

		return $this->classes[$mod];

	}


	// MAGIC
	public function __get( string $k ) {
		return $this->get( $k );
	}

}