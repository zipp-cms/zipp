class MediaMainPage {

	constructor() {
		AdminPages.listen( 'media', r => this.on( r ) );
	}

	async on( r ) {

		const l = r.lang;

		const itms = Media.convert( r.items );

		r.main = `
<div class="page-top">

	<h1>${ r.title }</h1>

	<div class="top-actions">
		${ SiteInt.mlDropDown( r ) }
		<a href="" class="tp-act tp-icon-add" data-action="upload">Upload</a>
	</div>

</div>

<div class="basic-cont">
	<div class="media-cont">${ itms.map( itm => itm.renderAsItem( r.editUrl + '/' + itm.id ) ).join( '' ) }</div>
</div>`;

		this.listenOnOpen();
		this.listenOnUpload();
		SiteInt.listenOnLang( r );

	}

	listenOnOpen() {
		ca('.media-item').c( el => {
			el.o( 'click', e => {
				e.preventDefault();

				AdminPages.loadPage( el.href );

			} );
		} );
	}

	listenOnUpload() {

		c('.tp-act[data-action="upload"]').o( 'click', e => {
			e.preventDefault();

			const pop = new UploadPop( 'media/upload' );

			pop.open();

		} );

	}

}

class MediaEditPage {

	constructor() {
		this.formCls = 'media-edit-form';
		AdminPages.listen( 'mediaedit', r => this.on( r ) );
	}

	async on( r ) {

		const l = r.lang;

		const fields = Fields.convert( r.fields );
		const itm = Media.convert( [r.item] )[0];

		this.saveBtn = new TpSaveBtn( l.saveBtn );

		r.main = `
<div class="page-top">

	<h1>${ itm.niceName }</h1>

	<div class="top-actions">
		<a href="" class="tp-act tp-icon-delete" data-action="delete">${ l.deleteBtn }</a>
		${ this.saveBtn.html }
	</div>

</div>

<div class="basic-cont">

	<div class="media-view">
		${ itm.render() }
	</div>

	<form method="POST" class="${ this.formCls }" data-ajax="mediaedit">

		<div class="form-msgs"></div>

		${ r.nonce }
		<input type="hidden" name="id" value="${ itm.id }">
	
		<div class="fields-grid">
			${ fields.map( f => f.html ).join( '' ) }
		</div>

	</form>

</div>`;

		this.saveBtn.init();

		fields.forEach( f => {
			f.listen();
			f.onChanged( () => this.saveBtn.changed() );
		} );
		
		AjaxForms.go( '.' + this.formCls );
		this.listenOnSave( r, itm.id );
		this.listenOnDelete( itm.id );
		this.listenOnLangDrop( r.langs );

		AjaxForms.listen( 'mediaedit', d => {
			if ( d.ok )
				AdminPages.reload();
		} );

	}

	listenOnSave( r ) {

		this.saveBtn.onClick( e => {
			AjaxForms.submit( '.' + this.formCls );
		} );

		DocEvents.listenOnSave( r.slug, e => {
			e.preventDefault();
			AjaxForms.submit( '.' + this.formCls );
		} );

		r.onLeft( () => {
			DocEvents.removeSaveListener( r.slug );
		} );

	}

	listenOnDelete( id ) {
		c('.tp-act[data-action="delete"]').o( 'click', e => {
			e.preventDefault();

			const pop = new DelMediaPop( 'media/delmedia/' + id );
			pop.open();

		} );
	}

	listenOnLangDrop( langs ) {

		const drop = c( `.${ this.formCls } [name="lang"]` );

		// if not in multilingual mode there is not lang dropdown
		// so ignore this
		if ( !drop )
			return;

		this.displayLangFields( drop.value, langs );

		drop.o( 'change', e => {
			this.displayLangFields( drop.value, langs );
		} );

	}

	displayLangFields( lang, langs ) {

		const form = c('.' + this.formCls);
		form.ca( '.hide' ).c( el => el.cl.remove( 'hide' ) );

		if ( lang === 'nulll' )
			return;

		langs.forEach( l => {
			if ( l !== lang )
				ca(`label[for="field-alt${ l }"], #field-alt${ l }`).c( el => el.cl.add('hide') );
		} );

	}

}