/*
@package: Zipp
@version: 0.1 <2019-08-09>
*/

'use strict';

class CheckBox extends Field {

	get value() {
		return this.el.checked;
	}

	get htmlField() {
		return `
		<label class="checkbox-cont" for="${ this.id }"><input ${ this.htmlId } type="checkbox" ${ this.htmlSlug } ${ this.initValue ? 'checked' : '' }><span class="renderer"></span></label>`;
	}

	// METHODS
	listen() {
		this.el = c(i(this.id));

		this.el.o( 'change', e => this.triggerChanges() );
	}

}
Fields.register( 'checkbox', CheckBox );