/*
@package: Zipp
@version: 1.0 <2019-06-13>
*/

'use strict';

// Dev Pack
AdminPages.listen( 'devpack', async r => {

	const l = r.lang;

	// need to implement widget functionallity

	const cfgs = [];

	for ( let k in r.cfgs ) {
		const obj = r.cfgs[k];
		obj.name = k;
		cfgs.push( obj );
	}

	r.main = `
<h1>${ r.title }</h1>

<div class="basic-cont">

	<form method="POST" class="dev-backup" data-ajax="devpack">
		${ r.nonce }
		<input type="hidden" name="action" value="backup">
		<div class="form-msgs"></div>
		<input type="submit" name="clear-tmp" value="${ l.backupBtn }">
	</form>

	<form method="POST" class="dev-pack" data-ajax="devpack">
		${ r.nonce }
		<input type="hidden" name="action" value="pack">
		<div class="form-msgs"></div>
		<input type="submit" name="clear-tmp" value="${ l.packBtn }">
	</form>

</div>`;

	AjaxForms.go( '.dev-backup' );
	AjaxForms.go( '.dev-pack' );

} );

AjaxForms.listen( 'devpack', r => {
	if ( r.ok )
		AdminPages.reload();
} );