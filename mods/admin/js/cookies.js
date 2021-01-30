/*
@package: Zipp
@version: 0.1 <2019-06-19>
*/

'use strict';

// IE not supported
class Cookies {

	static getAll() {
		let data = {};
		let d = document.cookie.split(';').forEach( p => {
			const ps = p.trim().split('=');
			data[ps[0]] = decodeURIComponent( ps[1] );
		} );
		return data;
	}

	static get( key, def = null ) {
		return tern( this.getAll(), key, def );
	}

	static set( key, value, days ) {
		const d = new Date;
		d.setTime( d.getTime() + days * 24 * 60 * 60 * 1000 );
		document.cookie = `${ key }=${ encodeURIComponent( value ) };expires=${ d.toUTCString() };path=/${ globalBasePath.trimStart( '/' ) }`;
	}

	static delete( key ) {
		this.set( key, null, -1 );
	}

}