/*
@package: Zipp
@version: 0.1 <2019-08-09>
*/

'use strict';

class EditorField extends Field {

	// GETTERS
	get value() {
		return this.input.value;
	}

	get htmlField() {
		return `
<div class="editor-cont" ${ this.htmlId }>
	<input type="hidden" ${ this.htmlSlug } value="${ this.initHtmlStrValue }">
	<div class="editor-actions">
		<a href="" class="edit-action" data-ed-action="revert"></a>
		<a href="" class="edit-action" data-ed-action="forward"></a>
		<a href="" class="edit-action" data-ed-action="clear"></a>
		<a href="" class="edit-action" data-ed-action="title"></a>
		<a href="" class="edit-action" data-ed-action="bold"></a>
		<a href="" class="edit-action" data-ed-action="italic"></a>
		<a href="" class="edit-action" data-ed-action="underline"></a>
		<a href="" class="edit-action" data-ed-action="strike"></a>
		<a href="" class="edit-action" data-ed-action="unorderedlist"></a>
		<a href="" class="edit-action" data-ed-action="orderedlist"></a>
	</div>
	<div class="editor-edit" contenteditable></div>
</div>`;
	}

	get html() {
		console.log( 'TODO: need to implement editor settings', this.sett );

		return `<div class="full-field">${ this.htmlInfo + this.htmlField }</div>`;

	}

	listen() {

		this.cont = c(i( this.id ));
		this.input = this.cont.c('input');
		this.editor = this.cont.c('.editor-edit');

		this.editor.innerHTML = this.input.value;

		// this.input.o( 'change', e => this.triggerChanges() );

		const ex = ( cmd, value = null ) => {
			return document.execCommand( cmd, false, value );
		};

		// make p's not div's
		ex( 'defaultParagraphSeparator', 'p' );

		this.cont.ca( '.edit-action' ).c( el => {
			el.o( 'click', e => {
				e.preventDefault();

				switch ( el.dataset.edAction ) {

					case 'revert':
						return ex( 'undo' );

					case 'forward':
						return ex( 'redo' );

					case 'clear':
						return ex( 'removeFormat' );

					// title

					case 'bold':
						return ex( 'bold' );

					case 'italic':
						return ex( 'italic' );

					case 'underline':
						return ex( 'underline' );

					case 'strike':
						return ex( 'strikeThrough' );

					case 'unorderedlist':
						return ex( 'insertUnorderedList' );

					case 'orderedlist':
						return ex( 'insertOrderedList' );

				}

				console.log( 'EDITOR: action not found', el.dataset.edAction );

			} );
		} );

		this.editor.o( 'input', e => {

			// TODO: check if we should do that

			/*if ( editor.innerHTML === '' )
				editor.innerHTML = '<p></p>';*/

			// should parse to make the output more consistent

			// prevents with double delete to have to p Tag
			/*if ( tern( v, 0, '<' ) !== '<' ) {
				v = '<p>' + v + '</p>';
				editor.innerHTML = v;
			}*/

			this.input.value = this.editor.innerHTML;

			this.triggerChanges();

		} );

	}

}
Fields.register( 'editor', EditorField );