/*
@package: Zipp
@version: 0.1 <2019-08-09>
*/

'use strict';

class TextField extends Field {

	// GETTERS
	get value() {
		return this.el.value;
	}

	get htmlField() {
		return `<input ${ this.htmlId } type="text" ${ this.htmlSlug } value="${ this.initHtmlStrValue }"${ this.settHtml }>`;
	}

	get settHtml() {
		const d = [];

		if ( tern( this.sett, 'req', false ) )
			d.push( 'required' );

		const max = tern( this.sett, 'max', 0 );
		if ( max > 0 )
			d.push( `maxlength="${ max }"` );
		
		const min = tern( this.sett, 'min', 0 );
		if ( min > 0 )
			d.push( `minlength="${ min }"` );

		return ' ' + d.join( ' ' );

	}

	// METHODS
	listen() { // should be executed when the html was inserted
		this.el = c(i(this.id));

		this.el.o( 'input', e => this.triggerChanges() );
	}

}
Fields.register( 'text', TextField );