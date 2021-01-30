/* Custom FIELDS */
class PageUrlField extends TextField {

	get value() {
		return this.el.value;
	}

	set value( v ) {
		this.el.value = v;
		this.triggerChanges();
	}

}
Fields.register( 'pageurl', PageUrlField );

class NavigationField extends Field {

	get value() {
		return '';
	}

	get htmlField() {
		return `<div ${ this.htmlId } class="navigation-cont"></div>`;
	}

	processData( data ) {
		this.pages = data.shift();
		this.lang = data.shift();
	}

	buildItem( pageId, level ) {
		return `
<div class="navigation-item">
	<!--<a href="" class="move only-icon o-icon-move" data-level="${ level.join() }"></a>-->
	<span>${ esc( this.pages[pageId] ) }</span>
	<a href="" class="add only-icon o-icon-add" data-level="${ level.join() }"></a>
	<a href="" class="remove only-icon o-icon-delete" data-level="${ level.join() }"></a>
</div>`;
	}

	build( data, levels ) {

		// console.log( 'data', data );

		if ( data.length === 0 )
			return '';

		return data.map( (d, i) => {
			const nLevel = levels.slice();
			nLevel.push( i );
			return `<div class="navigation-level">${ this.buildItem( d[0], nLevel ) + this.build( d[1], nLevel ) }</div>`;
		} ).join( '' );

	}

	reRender() {

		const cont = c(i(this.id));

		// i dont have language
		cont.h( `<input type="hidden" name="${ this.slug }" value="">` + this.build( this.initValue, [0] ) + `<a href="" class="add" data-level="0"><span class="only-icon o-icon-add"></span>${ this.lang.addBtn }</a>` );

		cont.s('input').value = JSON.stringify( this.initValue );

		this.goListen();

	}

	splitLevel( el ) {
		return el.dataset.level.split(',').map( l => parseInt( l ) );
	}

	removeLevel( levels ) {

		// remove first Layer
		levels.shift();

		// add first layer
		let itm = [0, this.initValue];

		// last
		const last = levels.pop();

		levels.forEach( l => {
			itm = itm[1][l];
		} );

		itm[1].splice( last, 1 );

	}

	addLevel( levels, data ) {

		// remove first Layer
		levels.shift();

		// add first layer
		let itm = [0, this.initValue];

		levels.forEach( l => {
			itm = itm[1][l];
		} );

		itm[1].push( [data, []] );

	}

	goListen() {

		const cont = c(i(this.id));

		cont.ca('.add').c( el => {
			el.o( 'click', async e => {
				e.preventDefault();

				const levels = this.splitLevel( el );

				const pop = new AddPagePopup( 'add-page-popup' );

				const pages = [];
				for ( let id in this.pages )
					pages.push( [id, this.pages[id]] );

				pop.open( {
					lang: this.lang,
					pages: pages
				} );

				const nId = await pop.selectedPage();

				if ( !nId )
					return;

				this.addLevel( levels, nId );
				this.reRender();
				this.triggerChanges();

			} );
		} );

		cont.ca('.remove').c( el => {
			el.o( 'click', e => {
				e.preventDefault();

				const levels = this.splitLevel( el );

				this.removeLevel( levels );

				this.reRender();
				this.triggerChanges();

				

			} );
		} );

	}

	listen() {
		this.reRender();
	}

}
Fields.register( 'navigation', NavigationField );



class PageField extends Field {

	get value() {
		return this.el.value;
	}

	get htmlField() {
		const options = [];

		if ( this.noPage )
			options.push([ 0, this.noPage ]);

		for ( let id in this.pages )
			options.push([ parseInt(id), this.pages[id] ]);

		const h = options.map( ([id, name]) =>
			`<option value="${ id }"${ this.initValue === id ? ' selected' : '' }>${ esc( name ) }</option>`
		).join('');

		return `<div class="select-cont"><select ${ this.htmlId } ${ this.htmlSlug }>${ h }</select></div>`;
	}

	listen() {
		this.el = c(i(this.id));

		this.el.o( 'change', e => this.triggerChanges() );
	}

	// init
	processData( data ) {
		this.pages = data.shift();
		this.noPage = data.shift();
	}

	exportData() {
		return [ this.pages, this.noPage ];
	}

}
Fields.register( 'page', PageField );