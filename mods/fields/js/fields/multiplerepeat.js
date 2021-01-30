/*
@package: Zipp
@version: 0.1 <2019-08-23>
*/

'use strict';

function mapI( ar, fn ) {
	const nAr = [];
	ar.forEach( ( v, i ) => nAr.push( fn( v, i ) ) );
	return nAr;
}

function addBtnCls( id ) {
	return `add-mr-${ id }`;
}

// NEED TO Refactor this or rewrite (maybe the entire field thing it looks like a mess)
class MultipleRepeat extends Field {

	// GETTERS
	get value() {
		return this.input.value;
	}

	get htmlField() {

		// initValue
		const cls = addBtnCls( this.id );

		return `
		<div class="repeat-cont" ${ this.htmlId }>
			<input type="hidden" ${ this.htmlSlug } value="">
			<div class="multiple-repeat-fields">${ this.fields.map( g => this.renderGroup( g ) ).join('') }</div>
			<a href="" class="add ${ cls }"><span class="only-icon o-icon-add"></span>${ esc( this.add ) }</a>
		</div>`;

	}

	// METHODS
	listen() {

		this.cont = c(i( this.id ));
		this.input = this.cont.c('input');
		this.addBtn = this.cont.c( '.' + addBtnCls( this.id ) );
		this.repFields = this.cont.c( '.multiple-repeat-fields' );

		this.fields.forEach( g => this.listenForGroup( g ) );

		this.addBtn.o( 'click', e => {
			e.preventDefault();

			const nG = this.templFields.map( f => f.clone() );

			this.repFields.be( this.renderGroup( nG ) );
			this.listenForGroup( nG );

			this.fields.push( nG );

			this.saveData();

		} );

		this.input.value = JSON.stringify( this.getData() );

	}

	getData() {
		return this.fields.map( g => g.map( f => f.value ) );
	}

	saveData() {
		this.input.value = JSON.stringify( this.getData() );
		this.triggerChanges();
	}

	listenForGroup( g ) {

		g.forEach( f => f.listen() );
		g.forEach( f => f.onChanged( () => this.saveData() ) );

		const removeBtn = this.cont.c(`.sr-remove[data-id="${ g[0].id }"]`);
		removeBtn.o( 'click', e => {
			e.preventDefault();

			this.fields = this.fields.filter( ng => ng[0].id !== g[0].id );

			removeBtn.parentElement.remove();

			this.saveData();

		} );

		

	}

	// INIT
	processData( data ) {
		this.add = data.shift();
		this.remove = data.shift();

		if ( isNil( this.initValue ) )
			this.initValue = [];

		this.templFields = data.shift().map( f => this.getField( f ) );

		this.fields = this.initValue.map( d => mapI( this.templFields, ( f, i ) => f.clone( tern( d, i, null ) ) ) );

	}

	exportData() {
		return [this.add, this.remove, this.templFields.map( f => f.export() )];// field
	}

	// PROTECTED
	getField( f ) {
		const type = f.shift(),
			slug = f.shift();
		const field = Fields.newField( type, slug, this.prefixedSlug );
		field.init( f );
		return field;
	}

	renderGroup( g ) {

		return `<div class="multiple-repeat-group"><div class="multiple-repeat-ctn">${ g.map( f => f.html ).join('') }</div><a href="" class="sr-remove only-icon o-icon-delete" title="${ esc( this.remove ) }" data-id="${ g[0].id }"></a></div>`;

	}

}
Fields.register( 'multiplerepeat', MultipleRepeat );