/*
@package: Zipp
@version: 0.2 <2019-07-19>
*/

'use strict';

class MediaUpload {

	// METHODS
	async build( r ) {

		// const l = r.lang;
		this.notAllowedText = r.notAllowed;

		this.el.h( `
<input type="file" id="${ this.fileRndId }" multiple accept="${ this.allowed.map( ext => '.' + ext ).join(',') }">
<span>${ r.uploadInfo }</span>
<div class="files-handler"></div>` );

		this.fileEl = c(i( this.fileRndId ));
		this.filesEl = this.el.c('.files-handler');

		this.listen();

	}

	onNewItem( fn ) {
		this.newItemFn = fn;
	}

	onStarted( fn ) {
		this.startedFn = fn;
	}

	onFinished( fn ) {
		this.finishedFn = fn;
	}

	// INIT
	constructor( allowed ) {
		this.el = c( '.upload-field-cont' );
		this.fileEl = null;
		this.fileRndId = randomToken( 8 );
		this.allowed = allowed;

		// on click
		// on drag

	}

	// PROTECTED
	listen() {

		this.el.o( 'click', e => {

			if ( e.target !== this.el )
				return;

			e.preventDefault();
			this.fileEl.click();
		} );

		this.el.o( 'drop', e => {
			e.preventDefault();
			this.el.cl.remove( 'over' );

			this.transfer( e.dataTransfer.files );
		} );


		this.fileEl.o( 'change', e => {
			this.transfer( this.fileEl.files );
			// this.fileEl.value = ''
			this.fileEl.value = '';
		} );


		// 
		this.el.o( 'dragover', e => e.preventDefault() );

		// styling
		this.el.o( 'dragenter', e => {
			e.preventDefault();
			this.el.cl.add( 'over' );
		} );

		this.el.o( 'dragleave', e => {
			e.preventDefault();
			this.el.cl.remove( 'over' );
		} );

	}

	// expect files
	async transfer( files ) {

		this.startedFn();

		// file type cannont be trusted

		const list = [];

		for ( let file of files ) {

			const extension = file.name.split('.').pop();
			if ( file.size <= 0 || file.type.length === 0 )
				continue;

			const handler = this.fileViewHandler();
			handler.show( file.name );

			if ( this.allowed.indexOf( extension ) === -1 ) {
				handler.error( this.notAllowedText );
				continue;
			}


			list.push( [ file, handler ] );

		}

		if ( list.length === 0 )
			return;

		const nonces = await this.getNonces( list.length );

		const listeners = [];

		for ( let i in list ) {
			// const type = file.type
			// const name = file.name
			const [file, handler] = list[i],
				nonce = nonces[i];


			listeners.push( this.upload( i, file, handler, nonce ) );

		}

		await Promise.all( listeners );

		this.finishedFn();

	}

	async getNonces( length ) {

		const res = await Ajax.json( 'mediaint', 'nonces', {
			length: length
		} );

		if ( !res.ok || res.data.length !== length ) {
			alert( res.data );
			throw new Error( res.data );
		}

		return res.data;

	}

	async upload( id, file, handler, nonce ) {

		const formData = new FormData;
		formData.append( 'id', id );
		formData.append( 'nonce', nonce );
		formData.append( 'file', file );

		const res = await Ajax.file( 'mediaint', 'upload', formData, perc => {
			handler.update( perc );
		} );

		if ( !res.ok ) {
			handler.error( res.data );
			return;
		}

		const itm = new MediaItem( res.data );

		handler.hide();


		this.newItemFn( itm );

	}

	fileViewHandler() {
		const self = this;
		return {
			el: null,
			exError: false,
			show( name ) {
				const rnd = randomToken(8);
				self.filesEl.be( `<p class="file-handler" id="${ rnd }" style="--progress:0%"><span>${ esc( name ) }</span></p>` );
				this.el = c(i( rnd ));
			},
			error( msg ) {
				this.el.be( `<span class="error">${ esc( msg ) }</span>` );
				this.update( 0 );
				if ( !this.exError )
					this.el.o( 'click', e => this.hide() );
				this.exError = true;
			},
			hide() {
				this.el.remove();
			},
			update( progress ) {
				this.el.setAttribute( 'style', `--progress:${ progress }%` );
			}
		};
	}

}