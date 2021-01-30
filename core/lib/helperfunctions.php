<?php
/*
@package: Zipp
@version: 0.1 <2019-05-27>
*/

// generate random string
function randomToken( int $l ) {

	$abc = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	$s = '';

	for ( $i = 0; $i < $l; $i++ )
		$s .= $abc[ random_int( 0, strlen( $abc ) - 1 ) ];

	return $s;

}

// hashes a password
function pwHash( string $str, int $cost = 12 ) {
	return password_hash( $str, PASSWORD_BCRYPT, [ 'cost' => $cost ] );
}

// verifies a password
function pwVerify( string $pw, string $hash ) {
	return password_verify( $pw, $hash );
}

// checks if an array has $num items
function has( array $ar, int $num = 1 ) {
	return count( $ar ) >= $num;
}

// short than isnull
// i dont like it, maybe changeto inil?
// null?
function isNil( $par ) {
	return is_null( $par );
}

// gets the length of a string or array
function len( $a ) {
	return is_array( $a ) ? count( $a ) : mb_strlen( $a, 'UTF-8' );
}

// check if a string has minium $len
function cLen( string $str, int $len = 1, int $max = 0 ) {

	if ( $max <= 0 )
		return len( $str ) >= $len;

	return len( $str ) >= $len && len( $str ) <= $max;

}

// convert a utf8 string to lowercase
function lower( string $s ) {
	return mb_strtolower( $s, 'UTF-8' );
}

// convert a utf8 string to uppercase
function upper( string $s ) {
	return mb_strtoupper( $s, 'UTF-8' );
}

// return date with hour
function now( bool $h = true ) {
	return date( 'Y-m-d'. ( $h ? ' H:i:s' : '' ) );
}

// include a file with args and "puts it out"
function includeWithArgs( string $path, array $args = [] ) {
	extract( $args );
	include( $path );
}

// include a file with args and returns it
function getIncludeWithArgs( string $path, array $args = [] ) {
	ob_start();
	includeWithArgs( $path, $args );
	return ob_get_clean();
}

// uint convert str to uint (if < 0) === $def (0)
function uInt( string $i, int $def = 0 ) {
	$i = (int) $i;
	return $i < 0 ? $def : $i;
}

// if bigger than max return max
function iMax( int $i, int $max ) {
	return $i > $max ? $max : $i;
}

// esc Html
function escHtml( string $str ) {
	return htmlspecialchars( $str, ENT_HTML5 | ENT_QUOTES, 'UTF-8' );
}

function e( string $str ) {
	return escHtml( $str );
}

// prevent Traversal
// this should be made better :)
// i think multiline is not required ?
// the input must be decoded first
function prevTrav( string $s ) {
	return preg_replace( '/[\.\\\\\/]{2,}/m', '/', $s );
}

// returns the executation time since the start
function calcExTime() {
	return round( ( microtime(true) - START_TIME ) * 1000, 3 );
}

// strip namespace
function stripNs( string $cls ) {
	return substr( $cls, strrpos( $cls, '\\' ) + 1 );
}

// parses an request uri
// removes the get header and decodes
function parseUri( string $uri ) {

	$pos = strpos( $uri, '?' );

	if ( !$pos )
		$pos = len( $uri );

	$uri = substr( $uri, 0, $pos );
	$uri = urldecode( trim( $uri, '/' ) );

	return prevTrav( $uri ). '/';

}

// removes trailing slashes at the end and adds one
function cleanUrl( string $url ) {
	return rtrim( $url, '/' ). '/';
}

// parses a string with commas into an array
function keywords( string $str ) {
	return cLen( $str ) ? array_map( 'trim', explode( ',', $str ) ) : [];
}