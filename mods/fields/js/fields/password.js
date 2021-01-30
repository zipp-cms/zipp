/*
@package: Zipp
@version: 0.1 <2019-08-09>
*/

'use strict';

class PasswordField extends TextField {

	get htmlField() {
		return `<input ${ this.htmlId } type="password" ${ this.htmlSlug } value="${ this.initHtmlStrValue }"${ this.settHtml }>`;
	}

}
Fields.register( 'password', PasswordField );