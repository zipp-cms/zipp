/*
@Author: Sören Meier <soerenmeier.ch>
@Created: 2019-05-22
@Version: 1.1.1 <2019-08-06>
*/

'use strict';

// id
const i = id => document.getElementById( id );

// el
const e = n => document.getElementsByTagName( n );

// single el (tag)
const se = n => document.getElementsByTagName( n )[0];

// single query
const s = q => document.querySelector( q );

// query all
const a = q => document.querySelectorAll( q );

// create Element
const ce = el => document.createElement( el );

// set innerHTML
const h = ( q, ht ) => s( q ).innerHTML = ht;

// addEventListener
const o = ( q, e, f ) => s( q ).addEventListener( e, f );

// ab insert html afterbegin
const ab = ( q, ht ) => s( q ).insertAdjacentHTML( 'afterbegin', ht );

// ae insert html afterend
const ae = ( q, ht ) => s( q ).insertAdjacentHTML( 'afterend', ht );

// bb insert html beforebegin
const bb = ( q, ht ) => s( q ).insertAdjacentHTML( 'beforebegin', ht );

// be insert html beforeend
const be = ( q, ht ) => s( q ).insertAdjacentHTML( 'beforeend', ht );


// convert element or singleQuery with converting
function c( el ) {

	el = typeof el === 'string' ? s( el ) : el;

	if ( !el )
		return false;

	// element by tag
	el.e = n => el.getElementsByTagName( n );

	// single element by tag
	el.se = n => el.getElementsByTagName( n )[0];

	// single query
	el.s = q => el.querySelector( q );

	// convert child with single query
	el.c = q => c( el.s( q ) );

	// query all
	el.a = q => el.querySelectorAll( q );

	// query all convert
	el.ca = q => ca( el.querySelectorAll( q ) );

	// set innerHTML
	el.h = ht => el.innerHTML = ht;

	// a short version for addEventListener
	el.o = ( ev, fn ) => el.addEventListener( ev, e => fn( e, el ) );

	// ab insert html afterbegin
	el.ab = ht => el.insertAdjacentHTML( 'afterbegin', ht );

	// ae insert html afterend
	el.ae = ht => el.insertAdjacentHTML( 'afterend', ht );

	// bb insert html beforebegin
	el.bb = ht => el.insertAdjacentHTML( 'beforebegin', ht );

	// be insert html beforeend
	el.be = ht => el.insertAdjacentHTML( 'beforeend', ht );

	// short for classList
	el.cl = el.classList;

	// short for previousElementSibling
	// this can maybe leed to inaccurate data because the previous Element is saved 
	el.prev = el.previousElementSibling;

	// short for nextElementSibling
	el.next = el.nextElementSibling;

	return el;

}


// convert nodeList
function ca( li ) {

	li = typeof li === 'string' ? a( li ) : li;

	// loop
	li.l = f => li.forEach( f );

	// loop convert
	li.c = f => li.forEach( el => f( c( el ) ) );

	// addEventListener for every element
	li.o = ( ev, fn ) => li.forEach( el => el.addEventListener( ev, e => fn( e, el ) ) );

	return li;

}

// escape a html string
function esc( html ) {

	const txt = document.createTextNode( html ),
		p = ce( 'p' );
	p.appendChild( txt );

	// maybe there is a better way to escape for attributes
	return p.innerHTML.replace( /"/g, '&#34;' ).replace( /'/g, '&#39;' );

}

// stands for ternary operator
function tern( a, b, c ) {

	if ( typeof c === 'undefined' )
		return !isNil( a ) ? a : ( typeof b === 'function' ? b() : b );

	if ( typeof a[b] === 'undefined' || a[b] === null )
		return typeof c === 'function' ? c() : c;

	return a[b];

}

function isNil( a, b ) {

	if ( typeof b === 'undefined' )
		return a === null || typeof a === 'undefined';

	return typeof a[b] === 'undefined' || a[b] === null;

}

const isString = a => typeof a === 'string';

const isArray = a => Array.isArray( a );

const isObject = a => typeof a === 'object';

const isNumber = a => typeof a === 'number';

function randomToken( l ) {

	const abc = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
	let s = '';

	for ( let i = 0; i < l; i++ )
		s += abc[Math.floor( Math.random() * abc.length )];

	return s;

}