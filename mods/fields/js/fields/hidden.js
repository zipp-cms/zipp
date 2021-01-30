/*
@package: Zipp
@version: 0.1 <2019-08-09>
*/

'use strict';

class HiddenField extends Field {

	// sett.type
	get value() {
		return this.el.value;
	}

	get htmlField() {
		console.log( 'TODO: need to implement setting for hidden Field', this.sett );
		return `<input ${ this.htmlId } type="hidden" ${ this.htmlSlug } value="${ this.initHtmlStrValue }">`;
	}

	// INIT
	processData( data ) {
		this.dontRenderInfo = true;
	}

	// METHODS
	listen() { // should be executed when the html was inserted
		this.el = c(i(this.id));

		this.el.o( 'change', e => this.triggerChanges() );
	}

}
Fields.register( 'hidden', HiddenField );