<?php
/*
@package: Zipp
@version: 0.2 <2019-07-01>
*/

namespace Data;

trait Module {

	protected $bHandler = null;

	protected $activeHandler = '';

	// protected $handlers = []; // array of supported // handler
	// database => 'DbUsers', filestorage => 'FsUsers'

	public function _getHandler() {

		if ( !isNil( $this->bHandler ) )
			return $this->bHandler;

		$active = null;
		$hndl = null;

		foreach ( ( $this->handlers ?? [] ) as $k => $cls ) {

			if ( $this->mods->has( $k ) ) {
				$active = $k;
				$cls = $this->cls( $cls );
				$hndl = new $cls( $this->mods );
				break;
			}

		}

		if ( isNil( $active ) )
			throw new Error( sprintf( 'Module %s needs a data handler', $this->slug ) );

		$this->bHandler = $hndl;
		$this->activeHandler = $active;

		return $this->bHandler;

	}

}