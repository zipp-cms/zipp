/*
@package: Zipp
@version: 1.0 <2019-06-19>
*/

'use strict';

class SiteInt {

	static mlDropDown( r ) {

		const langs = r.langsSelect,
			ml = r.multilingual,
			lang = r.baselang;

		if ( !ml )
			return '';

		return `
		<a class="tp-act tp-act-dropdown tp-icon-language" data-action="main-lang">
			<select name="main-lang">
				${ langs.map( l => `<option value="${ l }" ${ l === lang ? 'selected' : '' }>${ l.toUpperCase() }</option>` ).join( '' ) }
			</select>
		</a>`;

	}

	static listenOnLang( r ) {

		if ( !r.multilingual )
			return;

		const sel = c('.tp-act[data-action="main-lang"] select');

		sel.o( 'change', e => {
			// should check if can just switch
			this.setLang( sel.value );
			AdminPages.reload();
		} );

	}

	static setLang( value ) {
		Cookies.set( 'ctn-lang', value, 30 );
	}

}

class SettingsInteractor {

	constructor() {

		AdminPages.listen( 'settings', r => this.on( r ) );
		AdminPages.listenPrefix( 'tsp', r => this.on( r ) );

	}

	async on( r ) {

		const l = r.lang;

		this.fields = Fields.convert( r.fields );

		this.saveBtn = new TpSaveBtn( l.siteSave );

		r.main = `
<div class="page-top">

	<h1>${ r.title }</h1>

	<div class="top-actions">
		${ SiteInt.mlDropDown( r ) }
		${ this.saveBtn.html }
	</div>

</div>

<div class="basic-cont">
	
	<form method="POST" class="settings-form" data-ajax="${ r.slug }">
		<input type="hidden" name="baselang" value="${ r.baselang }">
		${ r.nonce }
		<input type="hidden" name="key" value="${ tern( r, 'key', '' ) }">
		<div class="form-msgs"></div>
		<div class="fields-grid">${ this.fields.map( f => f.html ).join( '' ) }</div>
	</form>

</div>`;

		this.fields.forEach( f => f.listen() );

		// this.saveBtn = c('.tp-act[data-action="save"]');
		this.saveBtn.init();

		AjaxForms.go( '.settings-form' );
		this.listenOnSave( r );
		SiteInt.listenOnLang( r );



	}

	listenOnSave( r ) {

		this.fields.forEach( f => f.onChanged( () => this.saveBtn.changed() ) );

		this.saveBtn.onClick( e => {
			AjaxForms.submit( '.settings-form' );
		} );

		DocEvents.listenOnSave( r.slug, e => {
			e.preventDefault();
			AjaxForms.submit( '.settings-form' );
			this.saveBtn.changeSaved();
		} );

		r.onLeft( () => {
			DocEvents.removeSaveListener( r.slug );
		} );

	}

}

const settings = new SettingsInteractor;