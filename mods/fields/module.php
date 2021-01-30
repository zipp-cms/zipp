<?php
/*
@package: Zipp
@version: 0.2 <2019-07-22>
*/

namespace Fields;

trait Module {

	public function addField( string $key, string $cls = null ) {
		if ( isNil( $cls ) )
			$cls = $this->cls( 'Fields\\'. $key );
		$this->mods->Fields->addField( $key, $cls );
	}

	public function addFields( array $clss ) {
		foreach ( $clss as $cls )
			$this->addField( $cls );
	}

	public function getField( string $key ) {
		return $this->mods->Fields->getField( $key );
	}

}