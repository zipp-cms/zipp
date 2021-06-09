class MediaItem {

	constructor( itm, baseLang ) {

		Object.assign( this, itm );
		this.baseLang = baseLang;

	}

	get value() {
		return `mediaId${ this.id }|${ this.baseLang }`;
	}

	get src() {
		return `${ this.preUrl + this.name }.${ this.type }`;
	}

	get niceName() {
		return `${ this.name }.${ this.type }`;
	}

	render() {

		if ( this.cat === 'img' )
			return `<img src="${ this.src }">`;

		return `no viewer`;

	}

	renderSqr() {

		if ( this.cat === 'img' )
			return `<div class="media-img-sqr" style="background-image: url('${ this.src }')"></div>`;

		return `no viewer`;

	}

	renderAsItem( href, cls ) {

		const tag = isNil( href ) ? 'div' : 'a';
		return `<${ tag } ${ href ? `href="${ href }" ` : '' }data-id="${ this.id }" class="media-item media-select-item media-cat-${ this.cat }${ tern( cls, '' ) }">
	${ this.renderSqr() }
	<span class="media-type">${ esc( this.type ) }</span>
	<span class="nice-name">${ esc( this.niceName ) }</span>
</${ tag }>`;

	}

}


class Media {

	static convert( items, baseLang ) {
		return items.map( itm => new MediaItem( itm, baseLang ) );
	}

}

class MediaInt {

	constructor() {
		const mainPage = new MediaMainPage;
		const editPage = new MediaEditPage;
	}

}

const mediaInt = new MediaInt;



