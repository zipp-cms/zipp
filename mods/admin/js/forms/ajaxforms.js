/*
@package: Zipp
@version: 0.1 <2019-05-29>
*/

'use strict';

class AjaxForms {

	static get mod() {
		return 'admin';
	}

	static init() {
		this.listening = {};
	}

	static listenMec( page, res, el ) {

		if ( isNil( this.listening, page ) )
			return true;

		if ( this.listening[page]( res, el ) )
				return false;

		return true;

	}

	static go( query ) {

		const form = new Form( query );
		form.onSubmit( form => this.submit( form ) );

	}

	// form can be query or a form object
	static async submit( form ) {

		if ( isString( form ) )
			form = new Form( form );

		Loader.show();

		const page = form.dataset.ajax;

		const res = await Ajax.form( this.mod, page, form.data() );

		if ( !this.listenMec( page, res, form ) )
			return Loader.hide();

		if ( res.type === 1 )
			form.nV( 'token', res.nonce );

		if ( res.ok ) {
			Loader.hide();
			return form.removeMsgs();
		}

		// here we need to do something
		form.error( res.data );

		Loader.hide();

	}

	static listen( page, fn ) {
		this.listening[page] = fn;
	}
}

AjaxForms.init();