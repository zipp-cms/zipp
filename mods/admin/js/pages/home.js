/*
@package: Zipp
@version: 0.1 <2019-06-13>
*/

class HomeWidget {

	// GETTERS
	get html() { return ''; }

	// METHODS
	async listen() {}

	async baseListen() {
		this.cont = c(i(this.id));
		return this.listen();
	}

	// INIT
	constructor( slug ) {
		this.id = randomToken( 5 );
		this.slug = slug;
	}

	// PROTECTED
	getHtml() {
		return `<div id="${ this.id }" class="widget ${ this.slug }">${ this.html }</div>`;
	}

}

class HomeWidgets {

	// METHODS
	static add( slug, cls ) {
		this.widgets[slug] = cls;
	}

	static getAll() {

		const clss = [];
		for ( let slug in this.widgets )
			clss.push( new this.widgets[slug]( slug ) );

		return clss;
	}

	static listen() {}

	// INIT
	static init() {
		this.widgets = {};
	}

}
HomeWidgets.init();

// Home
AdminPages.listen( 'home', async r => {

	const l = r.lang;

	// need to implement widget functionallity

	const widgets = HomeWidgets.getAll();

	r.main = `
<h1>${ l.homeTitle }</h1>

<div class="widgets-cont">

	${ widgets.map( w => w.getHtml() ).join('') }
	
	<!-- <div class="widget">
		
		<h2>Statistics</h2>

		<div class="widget-ctn-ph"></div>

	</div>

	<div class="widget">
		
		<h2>News</h2>

		<div class="widget-ctn-ph"></div>

	</div>-->

</div>`;

	widgets.forEach( w => w.baseListen() );

} );