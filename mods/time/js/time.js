/*
@package: Zipp
@version: 0.1.1 <2021-06-09>
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

	constructor( date ) {
		this.dateObj = date;
		this.lang = getHtmlLang();
	}

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

}

function isoToLocal( iso ) {
	const d = new Date( iso );
	// utc with local offset
	const date = new Date( d.getTime() - d.getTimezoneOffset() * 60000);
	// remove Z at the end
	return date.toISOString().split('.')[0];
}

function localToIso( local ) {
	return (new Date(local)).toISOString();
}

class TimeField extends TextField {

	get value() {
		return this.hidden.value;
	}

	get htmlField() {
		// shim the datetime input since we need utc
		let local = '';
		try {
			local = isoToLocal( tern( this.initValue, '' ) );
		} catch (e) {
			console.log('wrong date', e);
		}
		return `
<div class="time-cont">
<input id=${ this.id + '-hid' } type="hidden" ${ this.htmlSlug } value="${ this.initHtmlStrValue }">
<input ${ this.htmlId } type="datetime-local" value="${ local }">
</div>
`;
	}

	listen() {
		this.el = c(i(this.id));
		this.hidden = c(i(this.id + '-hid'));

		this.el.o( 'change', e => {
			this.hidden.value = localToIso( this.el.value );
		} );
	}

}
Fields.register( 'time', TimeField );