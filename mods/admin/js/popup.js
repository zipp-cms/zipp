/*
@package: Zipp
@version: 1.0 <2019-06-14>
*/

'use strict';

class PopUp {

	get cont() {
		return c(i( this.id ));
	}

	// METHODS
	async build( r ) {}

	async open() {

		Loader.show();

		const r = await AdminPages.getData( adminUrl( this.slug ) );
		if ( !r )
			return;

		const h = await this.build( r );

		c('body').be( this.coreBuild( h ) );

		this.listen();

		this.onOpen( r );

		Loader.hide();

	}

	close() {
		if ( this.onClose() === false )
			return;
		this.cont.remove();
	}

	addAction( action, title, full = false ) {
		this.actions += `<a href="" class="pop-act${ full ? ' full' : '' }" data-popaction="${ action }">${ title }</a>`;
	}

	onAction( action, fn ) {
		const el = this.cont.c(`[data-popaction="${ action }"]`);
		el.o( 'click', e => {
			e.preventDefault();
			fn( e, el );
		} );
	}

	selectActive( action ) {
		this.cont.s(`[data-popaction="${ action }"]`).focus();
	}

	// INIT
	constructor( slug ) {

		this.id = 'popup-' + randomToken(5);
		this.slug = slug;
		this.title = '';
		this.actions = '';
		this.small = false;
		this.headerIsShown = false;
		this.contCls = '';

	}

	// PROTECTED
	onOpen( r ) {}

	onClose() {}

	buildHeader( title ) {
		if ( title === '' )
			return '';
		this.headerIsShown = true;
		return `
<div class="pop-header">
	<h2>${ title }</h2>
	<a href="" class="close-pop"></a>
</div>`;
	}

	buildActions( ctn ) {
		if ( ctn === '' )
			return '';
		return `<div class="actions">${ ctn }</div>`;
	}

	coreBuild( h ) {
		return `
<div id="${ this.id }" class="popup pop-${ this.slug.replace( /\//g, '-' ) }">
	<div class="pop-cont ${ this.contCls }${ this.small ? ' pop-small' : '' }">
		${ this.buildHeader( this.title ) }
		${ h }
		${ this.buildActions( this.actions ) }
	</div>
</div>`;
	}

	listen() {

		const el = this.cont;

		el.o( 'click', e => {

			if ( e.target !== el )
				return;

			e.preventDefault();

			this.close();

		} );

		if ( this.headerIsShown ) {
			const cl = el.c('.close-pop');
			cl.o( 'click', e => {
				e.preventDefault();
				this.close();
			} );
		}

	}

}