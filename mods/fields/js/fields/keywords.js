/*
@package: Zipp
@version: 0.1 <2019-08-09>
*/

'use strict';

class KeywordsField extends TextField {

	get value() {
		return this.el.value.split( ',' ).map( v => v.trim() );
	}

	get htmlField() {
		console.log( 'TODO: need to implement settings and rendering for KeywordsField', this.sett );
		return `<input ${ this.htmlId } type="text" ${ this.htmlSlug } value="${ esc( tern( this.initValue, [] ).join(', ') ) }"${ this.settHtml }>`;
	}

	// METHODS
	listen() { // should be executed when the html was inserted
		this.el = c(i(this.id));

		this.el.o( 'change', e => this.triggerChanges() );
	}

}
Fields.register( 'keywords', KeywordsField );