/*
@package: Zipp
@version: 0.1 <2019-05-29>
*/

const timeLangs = {
	en: {
		months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
		shortMonths: ['Jan.', 'Feb.', 'Mar.', 'Apr.', 'May.', 'Jun.', 'Jul.', 'Aug.', 'Sep.', 'Oct.', 'Nov.', 'Dec.'],
		days: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
		shortDays: ['Su.', 'Mo.', 'Tu.', 'We.', 'Th.', 'Fr.', 'Sa.']
	},
	de: {
		months: ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'],
		shortMonths: ['Jan.', 'Feb.', 'Mar.', 'Apr.', 'Mai.', 'Jun.', 'Jul.', 'Aug.', 'Sep.', 'Okt.', 'Nov.', 'Dez.'],
		days: ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'],
		shortDays: ['So.', 'Mo.', 'Di.', 'Mi.', 'Do.', 'Fr.', 'Sa.']
	}
};

function getHtmlLang() {
	return s('html').getAttribute('lang');
}

class DateTime {

	get langStr() {
		return timeLangs[this.lang];
	}

	// Year
	get year() {
		return this.dateObj.getFullYear();
	}

	get shortYear() {
		return ('' + this.year()).substr( 2 );
	}

	// Month
	get month() {
		return this.dateObj.getMonth() + 1;
	}

	get padMonth() {
		return ('0' + this.month).substr( -2 );
	}

	get niceMonth() {
		return this.langStr.months[ this.dateObj.getMonth() ];
	}

	get niceShortMonth() {
		return this.langStr.shortMonths[ this.dateObj.getMonth() ];
	}

	// Date
	get date() {
		return this.dateObj.getDate();
	}

	get padDate() {
		return ('0' + this.date).substr( -2 );
	}

	// Day
	get day() {
		return this.dateObj.getDay();
	}

	get niceDay() {
		return this.langStr.days[ this.day ];
	}

	get niceShortDay() {
		return this.langStr.shortDays[ this.day ];
	}

	// Hour

	// Minute

	// Second

	// Millisecond

	get phpDate() {
		return `${ this.year }-${ this.padMonth }-${ this.padDate }`;
	}

	constructor( date ) {
		this.dateObj = date;
		this.lang = getHtmlLang();
		// how to get lang???
		// html
	}

}

function autoConvertDateTime() {

	ca( 'time[data-autoconvert]' ).l( el => {

		// convert to iso 8601
		// const utc = el.getAttribute( 'datetime' ).replace( ' ', 'T' ) + '.000Z';

		const utc = el.getAttribute( 'datetime' ),
			lang = el.dataset.lang,
			format = el.dataset.format;

		// const date = new Date(  );

		console.log( 'TODO time[data-autoconvert]', utc, lang, format );

		// el.

	} );

}

// Timezone auto convert
autoConvertDateTime();

if ( !isNil( Fields ) ) {

	// TODO implement TimeField
	class TimeField extends TextField {

		/*processData( data ) {
			this.sett = data.shift();
			this.value = data.shift();
		}*/

		/*render() {
			console.log( 'need to implement sett', this.sett );
			return this.rendInfo() + `<input id="${ this.id }" type="text" name="${ this.slug }" value="${ esc( tern( this.value, '' ).join(', ') ) }">`;
		}*/

	}
	Fields.register( 'time', TimeField );


}