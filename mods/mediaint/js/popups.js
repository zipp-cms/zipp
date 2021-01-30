/*
@package: Zipp
@version: 0.2 <2019-07-19>
*/

'use strict';

class UploadPop extends PopUp {

	onUploaded( fn ) {
		this.uploaded = fn;
	}

	// INIT
	async build( r ) {

		const l = r.lang;
		this.title = l.uploadTitle;
		this.uploading = false;

		return `
<div class="upload-field-cont"></div>`;

	}

	onOpen( r ) {

		const mediaUpload = new MediaUpload( r.allowed );

		mediaUpload.build( r );

		mediaUpload.onNewItem( itm => {
			//itms.push( itm );
		} );

		mediaUpload.onStarted( () => {
			this.uploading = true;
		} );

		mediaUpload.onFinished( () => {
			this.uploading = false;
			AdminPages.reload();
			this.close();
		} );

	}

	onClose() {
		if ( this.uploading )
			return false;
	}

}

class DelMediaPop extends PopUp {

	async build( r ) {

		const l = r.lang;
		this.small = true;
		this.title = l.deleteMedia;

		this.addAction( 'delete', l.deleteBtn, true );

		return `
<p>${ l.delQuestion }</p>
<form method="POST" class="media-delete-form" data-ajax="delmedia">
	${ r.nonce }
	<input type="hidden" name="id" value="${ r.mediaId }">
	<div class="form-msgs"></div>
</form>`;

	}

	onOpen() {

		AjaxForms.go( '.media-delete-form' );
		AjaxForms.listen( 'delmedia', d => {
			if ( d.ok ) {
				this.close();
				AdminPages.loadPage( d.data );
			}
		} );

		this.onAction( 'delete', e => AjaxForms.submit( '.media-delete-form' ) );

		this.selectActive( 'delete' );

	}

}

class SelectMediaPop extends PopUp {

	get isSingle() {
		return tern( this.single, false );
	}

	buildItem( itm, editUrl ) {

		const selected = this.isSingle ? ( itm.id === this.selected ) : ( this.selected.indexOf( itm.id ) >= 0 );

		// dont show if not allowed
		if ( !selected && this.allowed.indexOf( itm.type ) === -1 )
			return '';

		return itm.renderAsItem( '', selected ? ' selected' : '' );

	}

	async build( r ) {

		const l = r.lang;

		this.itms = Media.convert( r.items, r.baseLang );
		if ( isNil( this, 'selected' ) )
			this.selected = this.isSingle ? null : [];
		// this.selected = this.isSingle ? null : [];
		this.uploading = false;

		// title for header
		this.title = l.selectMediaTitle;

		this.addAction( 'cancel', l.cancel );
		this.addAction( 'select', l.insertSelMedia, true );

		return `
<div class="upload-field-cont"></div>
<h3>${ l.avMedia }</h3>
<div class="media-scroll-cont">
	<div class="media-select-items">
	${ this.itms.map( itm => this.buildItem( itm, r.editUrl ) ).join( '' ) }
	</div>
</div>`;

	}

	onOpen( r ) {

		const mediaUpload = new MediaUpload( this.allowed );

		r.notAllowed = this.notAllowed;
		mediaUpload.build( r );

		mediaUpload.onNewItem( itm => {
			const cont = c('.media-select-items').be( this.buildItem( itm, r.editUrl ) );
			this.itms.push( itm );
			this.listenItem( c( '.media-select-items .media-select-item:last-child' ), true );
		} );

		mediaUpload.onStarted( () => {
			this.uploading = true;
		} );

		mediaUpload.onFinished( () => {
			this.uploading = false;
		} );

		this.onAction( 'cancel', e => this.close() );

		this.onAction( 'select', e => {
			// select

			if ( this.selected === null || this.selected.length === 0 ) {
				this.selectedFn( this.selected );
				return this.close();
			}

			// convert selected
			const itms = this.getMediaById( this.isSingle ? [this.selected] : this.selected );

			this.selectedFn( this.isSingle ? itms[0] : itms );
			this.close();

		} );

		ca('.media-select-item').c( el => {
			this.listenItem( el );
		} );

	}

	onSelected( fn ) {
		this.selectedFn = fn;
	}

	onClose() {
		if ( this.uploading )
			return false;

		// clean for gb
		this.selectedFn = null;
	}

	getMediaById( ids ) {

		const itms = [];

		this.itms.forEach( itm => {
			if ( ids.indexOf( itm.id ) >= 0 )
				itms.push( itm );
		} );

		return itms;

	}

	listenItem( el, select = false ) {

		if ( select )
			this.selectItem( el );

		el.o( 'click', e => {
			e.preventDefault();

			if ( el.cl.contains( 'selected' ) )
				this.deselectItem( el );
			else
				this.selectItem( el );

		} );

	}

	selectItem( el ) {

		const id = parseInt( el.dataset.id );

		if ( this.isSingle ) {
			ca('.media-select-item.selected').c( sel => sel.cl.remove( 'selected' ) );
			this.selected = id;
		} else
			this.selected.push( id );

		el.cl.add( 'selected' );

	}

	deselectItem( el ) {

		const id = parseInt( el.dataset.id );

		if ( this.isSingle )
			this.selected = id;
		else
			this.selected = this.selected.filter( i => i !== id );

		el.cl.remove( 'selected' );

	}

}