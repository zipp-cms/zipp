/*
@package: Zipp
@version: 0.2 <2019-07-02>
*/

'use strict';

class UserInteractor {

	constructor() {

		AdminPages.listen( 'user', r => this.on( r ) );
		AdminPages.listen( 'logout', r => window.location.href = r.link );

	}

	async on( r ) {

		const l = r.lang;

		const user = r.user;
		this.fields = Fields.convert( r.fields );

		this.saveBtn = new TpSaveBtn( l.userSave );

		r.main = `
<div class="page-top">

	<h1>${ esc( user.surname ) } ${ esc( user.lastname ) }</h1>

	<div class="top-actions">
		${ this.saveBtn.html }
	</div>

</div>

<div class="basic-cont">
	<form method="POST" data-ajax="user" class="edit-user-form">
		<div class="form-msgs"></div>
		${ r.nonce }
		<div class="fields-grid">
			<label>${ l.usernameField }</label>
			<span>${ esc( user.username ) }</span>
			<label>${ l.roleField }</label>
			<span>${ user.niceRole }</span>
			${ this.fields.map( f => f.html ).join( '' ) }
		</div>
	</form>
</div>`;

		this.fields.forEach( f => f.listen() );

		this.saveBtn.init();

		AjaxForms.go( '.edit-user-form' );
		this.listenOnSave( r );

	}

	listenOnSave( r ) {

		this.fields.forEach( f => f.onChanged( () => this.saveBtn.changed() ) );

		this.saveBtn.onClick( e => {
			AjaxForms.submit( '.edit-user-form' );
		} );

		DocEvents.listenOnSave( 'userupdate', e => {
			e.preventDefault();
			AjaxForms.submit( '.edit-user-form' );
			this.saveBtn.changeSaved();
		} );

		AjaxForms.listen( 'user', d => {
			if ( d.ok )
				AdminPages.reload();
		} );

		r.onLeft( () => {
			DocEvents.removeSaveListener( 'userupdate' );
		} );

	}

}


const userInt = new UserInteractor;