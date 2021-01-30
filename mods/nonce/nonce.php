<?php
/*
@package: Zipp
@version: 0.1 <2019-05-29>
*/

namespace Nonce;

use Core\Module;

class Nonce extends Module {

	protected static function expires() {
		return 5 * 60 * 60; // 5h
	}

	public function get( string $k ) {
		return $this->sessGet( $k );
	}

	public function check( string $k, string $nonce ) {

		$d = $this->sessGet( $k );

		if ( isNil( $d ) )
			return false;

		$res = $this->cmp( $d, $nonce );

		$this->del( $k );
		return $res;

	}

	public function checkForm( string $k, object $data ) {

		$nonce = $data->token ?? null;

		if ( isNil( $nonce ) )
			return false;

		return $this->check( $k, $nonce );

	}

	public function new( string $k ) {
		$rnd = randomToken( 8 );
		$this->sessSet( $k, $rnd );
		return $rnd;
	}

	public function newForm( string $k ) {
		$rnd = $this->new( $k );
		return sprintf( '<input type="hidden" name="token" value="%s">', $rnd );
	}

	protected function cmp( array $d, string $nonce ) {

		$f = (int) $d[1];

		// expires 5h
		if ( $f + self::expires() < microtime(true) )
			return false;

		// maybe should use a more secure compare function
		if ( $d[0] !== $nonce )
			return false;

		return true;

	}

	protected function sess() {
		return $this->mods->Session;
	}

	protected function sessGet( string $k ) {
		$n = $this->mods->Session->get( 'nonce'. $k );
		return isNil( $n ) ? null : explode( ',', $n );
	}

	protected function sessSet( string $k, string $nonce ) {
		$this->mods->Session->set( 'nonce'. $k, sprintf( '%s,%d', $nonce, microtime(true) ) );
	}

	protected function del( string $k ) {
		$this->mods->Session->delete( 'nonce'. $k );
	}

}