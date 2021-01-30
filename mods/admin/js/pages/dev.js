/*
@package: Zipp
@version: 0.1 <2019-06-13>
*/

// Dev
AdminPages.listen( 'dev', async r => {

	const l = r.lang;

	// need to implement widget functionallity

	r.main = `
<h1>${ l.devTitle }</h1>

<div class="basic-cont">

	<table class="dev-info">
		<tr><td>${ l.devName }</td><td>${ l.devValue }</td></tr>
		<tr>
		${ r.infos.map( i => `<td>${ i[0] }</td><td>${ esc( i[1] ) }</td>` ).join('</tr><tr>') }
		</tr>
	</table>

	<!--<h3>Actions</h3>-->

	<form method="POST" class="clear-tmp" data-ajax="dev">
		${ r.nonce }
		<input type="hidden" name="action" value="clear">
		<div class="form-msgs"></div>
		<input type="submit" name="clear-tmp" value="${ l.clearTmp }">
	</form>

	<form method="POST" class="toggle-debug" data-ajax="dev">
		${ r.nonce }
		<input type="hidden" name="action" value="debug">
		<div class="form-msgs"></div>
		<input type="submit" name="toggle-debug" value="${ r.debug ? l.goInProd : l.goInDebug }">
	</form>
</div>`;

	AjaxForms.go( '.clear-tmp' );
	AjaxForms.go( '.toggle-debug' );

	AjaxForms.listen( 'dev', r => {
		if ( r.ok )
			AdminPages.reload();
	} );

} );