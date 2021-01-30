<?php
/*
@package: Magma PHP Template Engine
@author: Sören Meier <info@s-me.ch>
@version: 0.1 <2019-06-26>
*/

namespace MagmaTemplate;

class Blocks {

	protected static $blocks = [];

	public static function start() {
		ob_start();
	}

	public static function end( string $name ) {
		if ( !isset( self::$blocks[$name] ) )
			self::$blocks[$name] = ob_get_clean();
	}

	public static function out( string $name ) {
		echo self::$blocks[$name] ?? '';
	}

}