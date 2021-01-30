/*
@package: Zipp
@version: 0.1 <2019-08-09>
*/

'use strict';

class TextareaField extends TextField {

	get htmlField() {
		return `<textarea id="${ this.id }" name="${ this.slug }">${ this.initHtmlStrValue }</textarea>`;
	}

}
Fields.register( 'textarea', TextareaField );