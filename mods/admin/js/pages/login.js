/*
@package: Zipp
@version: 0.1 <2019-06-13>
*/

// Login
AjaxForms.listen( 'login', data => {

	if ( data.ok )
		window.location.reload();
} );


AdminPages.listen( 'login', async r => {

	const l = r.lang;

	const images = [
		[ 'https://images.unsplash.com/photo-1566262258598-53deb7089bf8?ixlib=rb-1.2.1&auto=format&fit=crop&w=1951&q=80', 'Matthieu Bühler', 'https://unsplash.com/@mattdreamsneon' ],
		[ 'https://images.unsplash.com/photo-1566305207270-a7d4cfa20fe6?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1950&q=80', 'Suzanne Emily O’Connor', 'https://unsplash.com/@suzanneemily' ],
		[ 'https://images.unsplash.com/photo-1565099824688-e93eb20fe622?ixlib=rb-1.2.1&auto=format&fit=crop&w=1351&q=80', 'Bharat Patil', 'https://unsplash.com/@bharat_patil_photography' ],
		[ 'https://images.unsplash.com/photo-1551949730-c0b55d675af1?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1351&q=80', 'Alexis Antoine', 'https://unsplash.com/@alexisantoine' ],
		[ 'https://images.unsplash.com/photo-1565274943681-6b0eecabad20?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80', 'Sylvia Szekely', 'https://unsplash.com/@sylviacreate' ],
		[ 'https://images.unsplash.com/photo-1564473185935-58113cba1e80?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1350&q=80', 'samuel sng', 'https://unsplash.com/@samuelsngx' ],
		[ 'https://images.unsplash.com/photo-1564490292125-2e3c78a0ef44?ixlib=rb-1.2.1&auto=format&fit=crop&w=675&q=80', 'Harshil Gudka', 'https://unsplash.com/@hgudka97' ],
		[ 'https://images.unsplash.com/photo-1564632720996-d3bf0b286a56?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjExMDk0fQ&auto=format&fit=crop&w=1350&q=80', 'Ash Edmonds', 'https://unsplash.com/@badashphotos' ],
		[ 'https://images.unsplash.com/photo-1564399331650-bbfe2aac0a04?ixlib=rb-1.2.1&auto=format&fit=crop&w=1268&q=80', 'Urip Dunker', 'https://unsplash.com/@uripdunker' ],
		[ 'https://images.unsplash.com/photo-1556726307-09a5d69f2cd8?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1350&q=80', 'Meriç Dağlı', 'https://unsplash.com/@meric' ]
	];

	const rndImage = images[Math.floor( Math.random() * images.length )];

	r.main = `
<div class="login-cont">
	<div class="login-img" style="background-image: url('${ rndImage[0] }')">
		<a href="${ rndImage[2] }" target="_blank">${ esc( l.imageBy.replace( '%s', rndImage[1] ) ) }</a>
	</div>
	<div class="login-box">
		<h2>${ l.loginTitle }</h2>

		<form method="POST" class="login-form" data-ajax="login">
			${ r.nonce }
			<label for="username">${ l.loginUsername }</label>
			<input type="text" id="username" name="username" placeholder="${ l.loginUsername }" required>
			<label for="password">${ l.loginPassword }</label>
			<input type="password" id="password" name="password" placeholder="${ l.loginPassword }">
			<div class="form-msgs"></div>
			<input type="submit" value="${ l.loginSubmit }">
		</form>
	</div>
</div>`;

	AjaxForms.go( '.login-form' );

} );