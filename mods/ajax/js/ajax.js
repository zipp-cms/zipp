/*
@package: Zipp
@version: 1.0 <2019-05-29>
*/

'use strict';

class Ajax {

	static get url() {
		return globalAjaxUrl;
	}

	static request( mod, event, data ) {
		return this.json( JSON.stringify( data ) );
	}

	static async form( mod, event, form ) {

		// form is formData

		return this.parseRequest( mod, event, 'fetch', {
				method: 'POST',
				body: form
			} );

	}

	static async file( mod, event, form, progressFn ) {
	
		return this.parseRequest( mod, event, 'xml', {
			method: 'POST',
			data: form,
			progress: progressFn
		} );

	}

	static async json( mod, event, data ) {

		return this.parseRequest( mod, event, 'fetch', {
			method: 'POST',
			body: JSON.stringify( data ),
			headers: {
				'Content-Type': 'application/json'
			}
		} );

	}

	static async parseRequest( mod, event, reqType, args ) {

		let ok = true,
			type = 0,
			data = {},
			time = '';

		try {

			const url = `${ this.url }${ mod }/${ event }/`;

			const res = reqType === 'xml' ?
				JSON.parse( await this.executeXmlRequest( url, args ) ) :
				await fetch( url, args ).then( res => res.json() );

			ok = res[0];
			type = parseInt( res[1] );
			data = res[2];
			time = res[3];

		} catch ( e ) {

			ok = false;
			data = e.message;
			time = '-';

		}

		// could move this entire thing up
		const obj = {
			ok: ok,
			type: type,
			data: data,
			time: time
		};

		if ( type === 1 ) {
			obj.data = data[0];
			obj.nonce = data[1];
		}

		console.log( `ajax ${ mod }.${ event } ex Time: ${ obj.time }` );

		return obj;

	}

	static async executeXmlRequest( url, args ) {
		return new Promise( ( resolve, error ) => {

			const req = new XMLHttpRequest;

			req.addEventListener( 'load', e => {
				args.progress( 100 );
				resolve( req.response );
			} );

			req.addEventListener( 'error', e => {
				console.log( 'error', req, e );
				// error( e );
				error();
			} );

			let prevProgress = 0;

			req.addEventListener( 'progress', e => {
				if ( e.lengthComputable ) {
					args.progress( ( e.loaded / e.total ) * 100 );
				} else {
					prevProgress += prevProgress === 75 ? 5 : 25;
					args.progress( prevProgress );
				}
			} );

			req.open( args.method, url );
			req.send( args.data );

		} );
	}

}