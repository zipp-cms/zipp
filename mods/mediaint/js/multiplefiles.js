class MultipleFilesField extends SingleFileField {

	get value() {
		return JSON.parse( this.input.value );
	}

	// GETTERS
	get htmlField() {

		let itms = [];
		if ( !isNil( this.initValue ) )
			itms = Media.convert( this.initValue );



		this.itms = itms;

		const hiddenInput = `<input type="hidden" ${ this.htmlSlug } value="${ esc( this.exportItems() ) }">`;

		// const view = `<div class="file-view">${ itm ? itm.renderAsItem() : '' }</div>`;
		const view = `<div class="file-views${ itms.length > 0 ? ' has-items' : '' }">${ itms.map( itm => itm.renderAsItem() ).join('') }</div>`;

		const buttons = `
<div class="buttons">
	<a href="" class="select-media">${ esc( this.select ) }</a>
</div>`;

		return `<div id="${ this.id }-cont" class="file-field multiple">${ hiddenInput + view + buttons }</div>`;
	}

	exportItems() {
		return JSON.stringify( this.itms.map( itm => itm.value ) );
	}

	processData( data ) {
		this.select = data.shift();
		this.notAllowed = data.shift();
		this.allowed = data.shift();
		this.itms = [];
	}

	listen() {

		const cont = c(i(`${ this.id }-cont`));
		const fileViews = cont.c('.file-views');
		this.input = cont.c('input');


		cont.c( '.select-media' ).o( 'click', e => {
			e.preventDefault();

			// should pass if single or double, and extensions
			const pop = new SelectMediaPop( 'media/select' );
			pop.allowed = this.allowed;
			pop.notAllowed = this.notAllowed;
			pop.selected = this.itms.map( itm => itm.id );

			pop.open();
			pop.onSelected( itms => {

				this.itms = itms;

				this.input.value = this.exportItems();
				fileViews.h( itms.map( itm => itm.renderAsItem() ).join('') );

				if ( this.itms.length > 0 )
					fileViews.cl.add( 'has-items' );
				else
					fileViews.cl.remove( 'has-items' );

				this.triggerChanges();

			} );

		} );

	}

}
Fields.register( 'multiplefiles', MultipleFilesField );