/*
@package: Zipp
@version: 1.0 <2019-06-13>
*/

class Loader {

	static toggle() {
		c('body').cl.toggle( 'loading' );
	}

	static show() {
		c('body').cl.add( 'loading' );
	}

	static hide() {
		c('body').cl.remove( 'loading' );
	}

}

class LangViewer {

	constructor( lang, data ) {

		this.lang = lang;
		this.data = data;

		return new Proxy( {}, {
			get: ( target, name ) => tern( this.data, name, () => `${ lang }.${ name }` )
		} );

	}

	getAll() {
		return this.data;
	}

}

class TpSaveBtn {

	// GETTERS
	get html() {
		return `<a href="" class="tp-act tp-icon-save" data-action="save">${ this.text }</a>`;
	}

	// METHODS
	changed() {
		this.hasChanged = true;
		this.el.cl.remove('tp-icon-save');
		this.el.cl.add('tp-icon-notsaved');
	}

	changeSaved() {
		this.hasChanged = false;
		this.el.cl.add('tp-icon-save');
		this.el.cl.remove('tp-icon-notsaved');
	}

	onClick( fn ) {
		this.clickFn = fn;
	}

	// INIT
	constructor( text ) {
		this.text = text;
		this.hasChanged = false;
		this.clickFn = () => {};
	}

	init() {
		this.el = c('.tp-act[data-action="save"]');

		this.el.o( 'click', e => {
			e.preventDefault();
			if ( this.clickFn( e ) !== false )
				this.changeSaved();
		} );

	}

}

class Page {

	static setTitle( title ) {
		document.title = buildTitle( title );
	}

}

class Nav {

	static render( sects, section, slug ) {

		if ( isNil( sects ) )
			return c('.main-container').cl.remove( 'show-nav' );

		this.pages = [];

		// set top Nav
		const out = sects.map( sc => {
			const itms = sc.sort( ( a, b ) => a[0] - b[0] );
			return itms.map( itm => this.buildTop( itm, section ) ).join( '' );
		} );

		c('.top-nav').h( `<div class="left-nav">${ out[0] }</div><div class="right-nav">${ out[1] }</div>` );

		// set Logo
		c('.logo-cont').h( `<span class="logo">Zipp</span>` );

		c('.sec-nav').h( this.pages.map( p => this.buildPage( p, slug ) ).join( '' ) );

		c('.main-container').cl.add( 'show-nav' );

		this.listen();

	}

	static buildTop( itm, sec ) {
		// maybe should encode the url
		const active = itm[1] === sec;

		if ( active )
			this.pages = itm[3];

		return `<a href="${ itm[3][0][2] }" data-nav="${ itm[1] }"${ active ? ' class="active"' : '' }>${ esc( itm[2] ) }</a>`;
	}

	static buildPage( p, slug ) {

		const active = p[0] === slug;

		return `<a href="${ p[2] }" data-nav="${ p[0] }" class="${ active ? 'active' : '' } page-icon-${ p[3] }">${ esc( p[1] ) }</a>`;

	}

	static listen() {

		ca( 'nav [data-nav]' ).c( el => {
			el.o( 'click', e => {
				e.preventDefault();
				AdminPages.loadPage( el.href );
			} )
		} );

	}

}

class DataRequest {

	constructor( d ) {

		Object.assign( this, d );

		this.leftEvents = [];
		this.lang = new LangViewer( this.activeLang, this.lang );

	}

	set main( data ) {
		s('main').innerHTML = data;
	}

	renderTitle() {
		Page.setTitle( this.title );
	}

	renderNav() {
		Nav.render( this.sections, this.section, this.slug );
	}

	onLeft( fn ) {
		this.leftEvents.push( fn );
	}

	left() {
		this.leftEvents.forEach( fn => fn() );
	}

}

class AdminPages {

	static async fetchData( url ) {

		const res = await fetch( url, {
			method: 'GET',
			headers: {
				'Req-Type': 'data'
			}
		} );

		try {
			const json = await res.clone().json();

			console.log( `req "${ url }" took ${ json[2] }` );

			return {
				ok: json[0],
				data: json[1]
			};

		} catch ( e ) {

			return {
				ok: false,
				data: await res.text()
			};

		}

	}

	static async getData( url ) {

		const data = await this.fetchData( url );

		if ( !data.ok )
			return this.error( data.data );

		return new DataRequest( data.data );

	}

	static async loadPage( url, push = true ) {

		Loader.show();

		const req = await this.getData( url );
		if ( !req )
			return;

		const fn = this.getListener( req.slug );
		if ( !fn )
			return this.error( `Page listener for ${ req.slug } not found!` );

		if ( !isNil( this.activeRequest ) )
			this.activeRequest.left();

		req.renderTitle();
		req.renderNav();

		if ( push )
			window.history.pushState( null, null, url );

		this.activeRequest = req;

		// maybe add the loader here and make the entire thing async
		await fn( req );

		Loader.hide();

	}

	static getListener( slug ) {

		if ( !isNil( this.listeners, slug ) )
			return this.listeners[slug];

		const parts = slug.split( '-' );

		if ( parts.length === 1 || isNil( this.preListeners, parts[0] ) )
			return false;

		return this.preListeners[parts[0]];

	}

	static reload() {
		this.loadPage( window.location.href, false );
	}

	// should be made better
	static error( msg ) {
		alert( msg );
		Loader.hide();
		return false;
	}

	static listen( slug, fn ) {
		this.listeners[slug] = fn;
	}

	static listenPrefix( pref, fn ) {
		this.preListeners[pref] = fn;
	}

	static init() {

		this.listeners = {};
		this.preListeners = {};
		this.activeRequest = null;

		window.addEventListener( 'popstate', e => {
			this.loadPage( window.location.href, false );
		} );

	}

}

AdminPages.init();