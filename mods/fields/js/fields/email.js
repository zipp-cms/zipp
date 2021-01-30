/*
@package: Zipp
@version: 0.1 <2019-08-09>
*/

'use strict';

class EmailField extends TextField {

	get htmlField() {
		return `<input ${ this.htmlId } type="email" ${ this.htmlSlug } value="${ this.initHtmlStrValue }"${ this.settHtml }>`;
	}

}
Fields.register( 'email', EmailField );