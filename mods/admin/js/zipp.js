/*
@package: Zipp
@version: 0.1 <2019-06-13>
*/

// GOOOOOOOOOOOOOOOOOOOOOO

class Zipp {

	static init() {

		// gets called when the website is loaded
		const url = window.location.href;

		// so the "animation" doenst look weird
		setTimeout( () => {
			AdminPages.loadPage( url );
		}, 400 );

	}

}
