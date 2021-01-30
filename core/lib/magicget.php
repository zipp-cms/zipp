<?php
/*
@package: Zipp
@version: 0.2 <2019-07-01>
*/

namespace Core;

class MagicGet {

	public function __get( string $k ) {

		$m = '_get'. ucfirst( $k );

		if ( method_exists( $this, $m ) )
			return $this->$m();

	}

}