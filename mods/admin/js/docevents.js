/*
@package: Zipp
@version: 0.2 <2019-07-02>
*/


class DocEvents {

	static listenOnSave( key, fn ) {
		this.saveEvents[key] = fn;
	}

	static removeSaveListener( key ) {
		delete this.saveEvents[key];
	}

	static init() {
		
		this.saveEvents = {};

		document.addEventListener( 'keydown', e => {

			const ctrl = ( window.navigator.platform.match('Mac') ? e.metaKey : e.ctrlKey );

			if ( ctrl && e.keyCode === 83 ) // s
				for ( let k in this.saveEvents )
					this.saveEvents[k]( e );

		} );

	}

}

DocEvents.init();