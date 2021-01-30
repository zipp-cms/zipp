/*
@package: Zipp
@version: 0.1 <2019-08-09>
*/

'use strict';

class Field {

	// GETTERS
	get value() { return null; }

	get initHtmlStrValue() {
		return esc( tern( this.initValue, '' ) );
	}

	get htmlId() { return `id="${ this.id }"`; }

	get htmlSlug() { return `name="${ this.prefixedSlug }"`; }

	get htmlInfo() {

		if ( this.dontRenderInfo )
			return '';

		const desc = isNil( this.desc ) ? null : `<p>${ this.desc }</p>`;
		const name = `<label for="${ this.id }" title="${ this.slug }">${ this.name }</label>`;

		if ( !isNil( desc ) )
			return `<div class="info">${ name + desc }</div>`;

		return name;

	}

	get htmlField() { return ''; }

	get html() { return this.htmlInfo + this.htmlField; }

	// METHODS
	onChanged( fn ) {
		this.changedFns.push( fn );
	}

	listen() { // should be executed when the html was inserted
	}

	// INIT
	constructor( type, slug, prefix = null ) {

		this.type = type;
		this.slug = slug;
		this.prefix = prefix;
		this.prefixedSlug = this.prefix ? `${ this.prefix }-${ this.slug }` : this.slug;
		this.id = 'field-' + this.prefixedSlug;
		this.changedFns = [];

	}

	init( data, rndId = false ) {

		if ( rndId )
			this.id += '-' + randomToken( 5 );

		this.name = data.shift();
		this.desc = data.shift();
		this.sett = data.shift();
		this.initValue = data.shift();

		// dont render Info

		this.processData( data );

	}

	clone( value, prefix = null ) {
		const d = this.export( value );
		const type = d.shift(),
			slug = d.shift();

		if ( !prefix )
			prefix = this.prefix;
		
		const field = Fields.newField( type, slug, prefix );
		field.init( d, true );
		return field;
	}

	// PROTECTED
	processData( data ) {}

	export( value ) {
		return [this.type, this.slug, this.name, this.desc, this.sett, value].concat( this.exportData() );
	}

	exportData() { return []; }

	triggerChanges() {
		this.changedFns.forEach( c => c( this ) );
	}

}