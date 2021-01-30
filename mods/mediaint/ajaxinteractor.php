<?php
/*
@package: Zipp
@version: 0.1 <2019-05-28>
*/

namespace MediaInt;

use Ajax\Interactor;
use Ajax\Request;
use Media\Media;

class AjaxInteractor extends Interactor {

	// public function
	// add to section ( Home, Content, Settings, Developer, User )
	// add to bereich ( Home, Inhalt, Einstellungen, Entwicklung, Benutzer )

	public function on( Request $req ) {

		if ( !$this->mods->Users->isLoggedIn() )
			return $req->error( $this->mod->lang->notLoggedIn );

		switch ( $req->event ) {

			case 'nonces':
				$this->getNonces( $req );
				break;

			case 'upload':
				$this->handleUpload( $req );
				break;

			default:
				$req->error( 'no event found' );
				break;

		}

	}

	protected function getNonces( Request $req ) {

		$length = (int) ( $req->data->length ?? 0 );
		if ( $length <= 0 )
			return $req->ok( [] );

		$nonce = $this->mods->Nonce;

		$nonces = [];
		for ( $i = 0; $i < $length; $i++ )
			$nonces[] = $nonce->new( 'upload'. $i );

		$req->ok( $nonces );

	}

	protected function handleUpload( Request $req ) {

		$d = $req->data;

		$id = (int) ( $d->id ?? 0 );
		$nonce = (string) ( $d->nonce ?? '' );

		if ( !$this->mods->Nonce->check( 'upload'. $id, $nonce ) )
			return $this->formError( $req );

		$f = $req->files->file ?? null;

		if ( !is_array( $f ) || !is_string( $f['name'] ) || !cLen( $f['name'] ) )
			return $this->formError( $req );

		$f = (object) $f;

		if ( $f->size <= 0 && $f->error > 0 )
			return $this->formError( $req );

		$l = $this->mod->lang;
		$ext = lower( pathinfo( $f->name, PATHINFO_EXTENSION ) );
		if ( !Media::checkExt( $ext, mime_content_type( $f->tmp_name ) ) )
			return $req->error( sprintf( $l->extensionError, $ext ) );

		// !Media::checkExt( $ext, mime_content_type( $f->tmp_name ) )
		$itm = $this->addFile( (object) [
			'name' => $this->sanitizeName( $f->name ),
			'type' => $ext,
			'tmp' => $f->tmp_name,
			'size' => $f->size
		] );

		// return media
		$req->ok( $itm->exportShort() );

	}

	protected function formError( Request $req ) {
		$req->error( $this->mod->lang->formError );
	}

	protected function sanitizeName( string $name ) {
		$n = lower( pathinfo( $name, PATHINFO_FILENAME ) );
		$n = preg_replace( '/[;?:@=&"\'<>#%{}|\\^~\/\[\]`\s]/', '-', $n );
		return substr( $n, 0, 26 ); // 26 because (30 max) - 4 > (_100 from random)
	}

	protected function addFile( object $file ) {

		// the file is valid to upload and where now able to move it and to the right folder
		$path = $this->mods->Media->getPath();
		if ( !is_dir( $path ) )
			mkdir( $path );

		$rnd = '';
		$c = 0;
		$nP = sprintf( '%s%s.%s', $path, $file->name, $file->type );

		// i dont like this parts :/

		while( is_file( $nP ) ) {

			$c++;
			if ( $c > 10 )
				throw new Error( 'could not find a file match please upload a file with another name' );

			$rnd = randomToken( 3 );
			$nP = sprintf( '%s%s_%s.%s', $path, $file->name, $rnd, $file->type );

		}

		if ( cLen( $rnd ) )
			$file->name .= '_'. $rnd;

		move_uploaded_file( $file->tmp, $nP );

		return $this->mods->Media->new( $file->name, $file->type, $file->size );

	}

}