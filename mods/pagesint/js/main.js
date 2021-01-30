/*
@package: Zipp
@version: 0.1 <2019-06-19>
*/

'use strict';

class Pages {

	constructor() {

		// this variable doenst get freed after a page switch
		this.pages = [];

		AdminPages.listen( 'pages', r => this.on( r ) );

		AdminPages.listenPrefix( 'tpc', r => this.on( r ) );

		// how do we do dynamic stuff???? mh...

	}

	buildPage( p, url, layoutTitle ) {

		return `
<a href="${ url }${ p.id }" class="page" data-id="${ p.id }" title="${ p.id }">
	<h2>${ esc( p.title ) }</h2>
	<div class="langs">${ p.langs.map( l => `<span data-state="${ l.state }">${ l.lang }</span>` ).join( '' ) }</div>
	<span class="layout">${ layoutTitle }: ${ p.layout }</span>
	<div class="page-actions"></div>
</a>`;

	}

	async on( r ) {

		const l = r.lang;

		this.pages = r.pages;
		const pages = r.pages.map( p => this.buildPage( p, r.editUrl, l.pageLayout ) ).join( '' );


		r.main = `
<div class="page-top">

	<h1>${ r.title }</h1>

	<div class="top-actions">
		<a href="" class="tp-act tp-icon-add" data-action="new-page">${ l.newPage }</a>
	</div>

</div>

<div class="pages-cont">
	
	<div class="pages-actions">
		<div class="pages-search-bar">
			<input type="text" class="search-bar" placeholder="${ l.searchField }">
			<span class="search-icon"></span>
		</div>
	</div>

	<div class="pages">
		${ pages }
	</div>

	<div class="pages-info">
		${ l.pagesInfo }:
		<a data-state="2">${ l.stateLive }</a>
		<a data-state="1">${ l.statePreview }</a>
		<a data-state="0">${ l.stateArchive }</a>
	</div>

</div>`;

	this.listenOnSearch();
	this.listenOnNewPage();
	this.listenOnPageEdit();

	}

	listenOnSearch() {

		const cont = c('.pages-search-bar'),
			inp = cont.c('.search-bar');

		inp.o( 'input', e => {
			this.search( inp.value );
		} );

	}

	search( v ) {

		this.pages.forEach( p => {

			this.display( p.id, this.cmp( v, p.id ) || this.cmp( v, p.title ) || this.cmp( v, p.keywords.join() ) );

		} );

	}

	cmp( a, b ) {

		a = a.toLowerCase();
		b = (b + '').toLowerCase();

		return b.indexOf( a ) > -1;

	}

	display( id, show ) {

		const el = c(`.page[data-id="${ id }"]`);

		if ( show )
			return el.cl.remove( 'hide' );

		el.cl.add( 'hide' );

	}

	listenOnNewPage() {

		const el = c('[data-action="new-page"]');

		el.o( 'click', e => {

			e.preventDefault();

			const pop = new NewPagePop( 'pages/new' );

			pop.open();

		} );

	}

	listenOnPageEdit() {

		ca('.pages .page').c( el => {

			el.o('click', e => {
				e.preventDefault();

				AdminPages.loadPage( el.href );

			});

		} );

	}

}

const pages = new Pages;


class EditPage {


	constructor() {
		AdminPages.listen( 'editpage', r => this.on( r ) );
	}

	async on( r ) {

		const l = r.lang;

		const mf = {};
		Fields.convert( r.mainF ).forEach( f => mf[f.slug] = f );

		const comps = r.comps.map( c => {
			c.fields = Fields.convert( c.fields );
			return c;
		} );

		const compsH = comps.map( c => {
			const fields = c.fields.map( f => f.html ).join( '' );
			return `<h3>${ c.name }</h3>
			<div class="fields-grid">${  fields }</div>`;
		} ).join( '' );

		// console.log( mf, comps );

		this.saveBtn = new TpSaveBtn( l.saveBtn );

		r.main = `
<div class="page-top">

	<h1>${ esc( mf.title.initValue ) }</h1><a href="" class="view-link only-icon o-icon-view" target="_blank"></a>

	<div class="top-actions">
		${ SiteInt.mlDropDown( r ) }
		<a href="" class="tp-act tp-icon-delete" data-action="delete-page">${ l.deleteBtn }</a>
		${ this.newLangBtn( r ) }
		${ this.saveBtn.html }
	</div>

</div>

<div class="page-cont real">

	<form method="POST" class="edit-page-form" data-ajax="editpage">

		${ r.nonce }
		${ mf.id.html }
		${ mf.ctnId.html }
		${ mf.lang.html }
		${ mf.layout.html }
	
		<div class="page-switcher">
			<a href="" data-tab="content" class="active"><h2>${ l.contentTab }</h2></a>
			<a href="" data-tab="options"><h2>${ l.optionTab }</h2></a>
		</div>

		<div class="form-msgs"></div>

		<div class="tab page-content show">
			<div class="db-fields-grid">
				<div>${ mf.title.html }</div>
				<div>${ mf.url.html }</div>
			</div>

			${ compsH }

		</div>

		<div class="tab page-options">
			<div class="fields-grid">
				${ mf.state.html }
				${ mf.publishOn.html }
				${ mf.keywords.html }
			</div>
		</div>

	</form>

</div>`;

		AjaxForms.go( '.edit-page-form' );

		this.saveBtn.init();

		for ( let k in mf ) {
			mf[k].listen();
			mf[k].onChanged( () => this.saveBtn.changed() );
		}

		comps.forEach( c => c.fields.forEach( f => {
			f.listen();
			f.onChanged( () => this.saveBtn.changed() );
		} ) );

		this.listenOnTabSwitch();
		this.h1 = c('.page-top h1');
		this.listenOnTitleChange( mf );
		this.listenOnUrlChange( r, mf );
		this.listenOnSave( r );
		SiteInt.listenOnLang( r );
		this.listenOnNewLang( r, mf );
		this.listenOnDelete( mf );

	}

	newLangBtn( r ) {

		if ( !r.multilingual || !r.couldCreateNewLang )
			return '';

		return `<a href="" class="tp-act tp-icon-add" data-action="new-lang">${ r.lang.newLang }</a>`;

	}


	listenOnTabSwitch() {

		const cont = c('.page-switcher');

		cont.ca( '[data-tab]' ).c( el => {
			el.o( 'click', e => {
				e.preventDefault();

				const tab = el.dataset.tab;

				cont.c( '.active' ).cl.remove('active');
				el.cl.add('active');

				c('.page-cont .tab.show').cl.remove('show');
				c(`.page-cont .page-${ tab }`).cl.add('show');

			} );
		} );

	}

	listenOnTitleChange( mf ) {

		mf.title.onChanged( () => {
			mf.url.value = this.convertToUrl( mf.title.value );
			this.h1.h( esc( mf.title.value ) );
		} );

	}

	convertToUrl( str ) {
		return str.toLowerCase().replace( /\s/g, '-' ).replace( /[;\/?:@=&"<>#%{}|\\^~\[\]`]/g, '' ) + '/';
	}

	listenOnUrlChange( r, mf ) {

		mf.url.onChanged( () => {
			this.buildViewLink( r, mf );
		} );

		this.buildViewLink( r, mf );

	}

	buildViewLink( r, mf ) {
		const lang = r.multilingual ? `${ mf.lang.value }/` : '';
		c('.view-link').href = coreUrl( lang + mf.url.value ).replace(/\/+$/, '') + '/';
	}

	listenOnSave( r ) {

		this.saveBtn.onClick( e => {
			AjaxForms.submit( '.edit-page-form' );
		} );

		DocEvents.listenOnSave( r.slug, e => {
			e.preventDefault();
			AjaxForms.submit( '.edit-page-form' );
			this.saveBtn.changeSaved();
		} );

		r.onLeft( () => {
			DocEvents.removeSaveListener( r.slug );
		} );

	}

	listenOnNewLang( r, mf ) {

		if ( !r.multilingual || !r.couldCreateNewLang )
			return '';

		c('.tp-act[data-action="new-lang"]').o( 'click', e => {
			e.preventDefault();

			const id = mf.id.value;

			const pop = new NewLangPop( 'pages/newlang/' + id );

			pop.open();

		} );

	}

	listenOnDelete( mf ) {

		c('.tp-act[data-action="delete-page"]').o( 'click', e => {
			e.preventDefault();

			const id = mf.id.value;
			const ctnId = mf.ctnId.value;

			const pop = new DelLangPop( `pages/delpage/${ id }/${ ctnId }/` );

			pop.open();

		} );

	}

}

const editPage = new EditPage;