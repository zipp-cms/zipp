/*
@package: Zipp
@version: 1.0 <2019-05-29>
*/

'use strict';

class Form {

	constructor( el ) {

		this.el = c( el );

		this.disabled = false;

	}

	onSubmit( fn ) {

		this.el.o( 'submit', async e => {
			e.preventDefault();

			if ( this.disabled )
				return;

			this.disable();
			await fn( this );
			this.enable();

		} );

	}

	disable() { // disable form submittion
		this.disabled = true;
		this.el.s( '[type="submit"]' ).setAttribute( 'disabled', '' );
	}

	enable() { // enable form submittion
		this.disabled = false;
		this.el.s( '[type="submit"]' ).removeAttribute( 'disabled' );
	}

	e( field ) {

		if ( isNil( this.el, field ) )
			throw new Error( `field: ${ field } could not be found` );

		return this.el[field];

	}

	v( field ) {

		const el = this.e( field );
		return el.value;

	}

	nV( field, value = '' ) {
		const el = this.e( field );
		el.value = value;
	}

	r( field ) { // reset field
		this.nV( field );
	}

	data() {
		return new FormData( this.el );
	}

	get dataset() {
		return this.el.dataset;
	}

	static newNotice( s, cls = 'error' ) { // html is not escaped
		return `<p class="notice ${ cls }">${ esc( s ) }</p>`;
	}

	error( msg ) {

		const el = this.el.c( '.form-msgs' );

		if ( !el )
			throw new Error( 'Please define a form-msgs div for the form' );

		el.h( this.constructor.newNotice( msg ) );
	
	}

	removeMsgs() {
		this.el.c( '.form-msgs' ).h( '' );
	}

	// TODO check if we should add this
	/*warning( msg ) {
		el.s( '.form-msgs' ).innerHTML = this.newNotice( msg, 'warning' );
	}*/

}