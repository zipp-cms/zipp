<?php
/*
@package: Zipp
@version: 0.1 <2019-05-27>
*/

define( 'START_TIME', microtime( true ) );

require_once( './core/startup.php' );

// lets start
Core\KERNEL::start();


if ( !defined( 'DONT_OUTPUT_TIME' ) && php_sapi_name() !== 'cli' )
	echo '<script>console.log("ex Time: '. calcExTime(). ' ms")</script>';