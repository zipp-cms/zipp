/*
@package: Zipp
@version: 0.1 <2019-08-09>
*/

'use strict';

class NumberField extends Field {

	// GETTERS
	get value() {
		return parseInt( this.el.value );
	}

	get htmlField() {
		return `<input ${ this.htmlId } type="number" ${ this.htmlSlug } value="${ tern( this.initValue, 0 ) }"${ this.settHtml }>`;
	}

	get settHtml() {
		const d = [];

		const max = tern( this.sett, 'max', 0 );
		if ( max > 0 )
			d.push( `max="${ max }"` );
		
		const min = tern( this.sett, 'min', 0 );
		if ( min > 0 )
			d.push( `min="${ min }"` );

		return ' ' + d.join( ' ' );
	}

	// METHODS
	listen() { // should be executed when the html was inserted
		this.el = c(i(this.id));

		this.el.o( 'change', e => this.triggerChanges() );
	}

}
Fields.register( 'number', NumberField );