class NewPagePop extends PopUp { // maybe should be a modal

	async build( r ) {

		const l = r.lang;

		this.title = l.newPageTitle;
		this.fields = Fields.convert( r.fields );

		this.addAction( 'newpage', l.newPage, true );

		return `
<form method="POST" class="form-new-page" data-ajax="newpage">
	${ r.nonce }
	<div class="fields-grid">
		${ this.fields.map( f => f.html ).join( '' ) }
	</div>
	<div class="form-msgs"></div>
</form>`;

	}

	onOpen() {

		this.fields.forEach( f => f.listen() );
		AjaxForms.go( '.form-new-page' );
		AjaxForms.listen( 'newpage', d => {
			if ( d.ok ) {
				this.close();
				AdminPages.loadPage( d.data );
			}
		} );

		this.onAction( 'newpage', e => {
			AjaxForms.submit( '.form-new-page' );
		} );

	}

}

class NewLangPop extends PopUp { // maybe should be a modal

	async build( r ) {

		const l = r.lang;

		this.title = l.newPageTitle;
		this.fields = Fields.convert( r.fields );

		this.addAction( 'newlang', l.newPage, true );

		// should show a lang dropdown with available langs 
		// should show a dropdown with langs that are able to be coppied

		return `
<form method="POST" class="form-new-page-lang" data-ajax="newpagelang">
	${ r.nonce }
	<input type="hidden" name="pageId" value="${ r.pageId }">
	<div class="fields-grid">
		${ this.fields.map( f => f.html ).join( '' ) }
	</div>
	<div class="form-msgs"></div>
</form>`;

	}

	onOpen() {
		// this.fields.forEach( f => f.listen() );
		AjaxForms.go( '.form-new-page-lang' );
		AjaxForms.listen( 'newpagelang', d => {

			if ( d.ok ) {
				SiteInt.setLang( d.data );
				this.close();
				AdminPages.reload();
			}

		} );

		this.onAction( 'newlang', e => AjaxForms.submit( '.form-new-page-lang' ) );

	}

}

class DelLangPop extends PopUp { // maybe should be a modal

	async build( r ) {

		this.small = true;

		const l = r.lang;

		this.title = l.deleteLang;
		this.nonce = r.nonce;

		this.addAction( 'delete', l.deleteForever, true );
		this.addAction( 'archive', l.archiveBtn );

		// this.fields = Fields.convert( r.fields );

		// should show a lang dropdown with available langs 
		// should show a dropdown with langs that are able to be coppied

		return `
<p>${ l.delOption }</p>
<form method="POST" class="del-page-lang" data-ajax="delpage">
	${ r.nonce }
	<input type="hidden" name="pageId" value="${ r.pageId }">
	<input type="hidden" name="ctnId" value="${ r.ctnId }">
	<input type="hidden" name="type" value="">
	<div class="form-msgs"></div>
</form>`;

	}

	onOpen() {

		const cls = '.del-page-lang';
		// this.fields.forEach( f => f.listen() );
		AjaxForms.go( cls );
		AjaxForms.listen( 'delpage', d => {

			if ( d.ok ) {
				this.close();
				if ( d.data === 'reload' )
					AdminPages.reload();
				else
					AdminPages.loadPage( d.data );
			}

		} );

		const typeEl = s(cls + ' [name="type"]');

		this.onAction( 'delete', e => {
			typeEl.value = 'delete';
			AjaxForms.submit( cls );
		} );

		this.onAction( 'archive', e => {
			typeEl.value = 'archive';
			AjaxForms.submit( cls );
		} );

	}

}


class AddPagePopup extends PopUp {

	// METHODS
	async open( r ) {

		const h = await this.build( r );

		c('body').be( this.coreBuild( h ) );

		this.listen();

		this.onOpen( r );

	}

	selectedPage() {
		return new Promise( resolve => {
			this.selectedFn = resolve;
		} );
	}

	// PROTECTED
	build( r ) {

		this.small = true;

		const l = r.lang;
		this.title = l.selectTitle;

		this.addAction( 'cancel', l.selectCancel );
		this.addAction( 'select', l.selectBtn, true );

		return `<div class="select-cont"><select class="navigation">
			${ r.pages.map( p => `<option value="${ p[0] }">${ p[1] }</option>` ).join('') }
		</select></div>`;
	}

	onOpen() {

		this.onAction( 'select', e => {
			this.selectedFn( parseInt( this.cont.c('select').value ) );
			this.close();
		} );

		this.onAction( 'cancel', e => this.close() );

	}

	onClose() {
		if ( this.selectedFn )
			this.selectedFn( NaN );
	}

}