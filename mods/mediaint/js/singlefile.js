class SingleFileField extends Field {

	// GETTERS
	get value() {
		return this.input.value === '' ? null : this.input.value;
	}

	get htmlField() {

		let itm = false;
		if ( !isNil( this.initValue ) )
			itm = Media.convert( [this.initValue] )[0];

		this.itm = itm;

		const hiddenInput = `<input type="hidden" name="${ this.slug }" value="${ itm ? esc( itm.value ) : 'null' }">`;

		const view = `<div class="file-view">${ itm ? itm.renderAsItem() : '' }</div>`;

		const buttons = `
<div class="buttons${ itm ? '' : ' no-item' }">
	<a href="" class="select-media">${ esc( this.select ) }</a>
	<a href="" class="remove-media only-icon o-icon-delete"></a>
</div>`;

		return `<div id="${ this.id }-cont" class="file-field single">${ hiddenInput + view + buttons }</div>`;
	}

	processData( data ) {
		this.select = data.shift();
		this.notAllowed = data.shift();
		this.allowed = data.shift();
		this.itm = false;
	}

	exportData() {
		return [ this.select, this.notAllowed, this.allowed ];
	}

	listen() {

		const cont = c(i(`${ this.id }-cont`));
		const fileView = cont.c('.file-view');
		this.input = cont.c('input');
		const buttons = cont.c('.buttons');
		buttons.withItem = function() { this.cl.remove('no-item') };
		buttons.noItem = function() { this.cl.add('no-item') };


		cont.c( '.select-media' ).o( 'click', e => {
			e.preventDefault();

			// should pass if single or double, and extensions
			const pop = new SelectMediaPop( 'media/select' );
			pop.single = true;
			pop.allowed = this.allowed;
			pop.notAllowed = this.notAllowed;
			if ( this.itm )
				pop.selected = this.itm.id;

			pop.open();
			pop.onSelected( itm => {

				if ( !itm )
					return;

				this.itm = itm;

				this.input.value = itm.value;
				fileView.h( itm.renderAsItem() );
				buttons.withItem();

				this.triggerChanges();

			} );

		} );

		cont.c('.remove-media').o( 'click', e => {
			e.preventDefault();

			this.input.value = '';
			this.itm = null;
			fileView.h('');
			buttons.noItem();
			this.triggerChanges();

		} );

	}

}
Fields.register( 'singlefile', SingleFileField );