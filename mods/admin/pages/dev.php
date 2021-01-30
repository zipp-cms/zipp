<?php
/*
@package: Zipp
@version: 0.1 <2019-06-14>
*/

namespace Admin\Pages;

use Admin\{Page, DataRequest};
use Router\Interactor;
use Router\Request;
use Core\FS;
use Ajax\Request as AjaxRequest;

class Dev extends Page {

	protected $section = 'dev';

	protected $slug = 'dev';

	protected $template = 'dev';

	public function onData( DataRequest $req ) {

		if ( !$this->isAdmin() )
			return $l->authError;

		$r = $this->mods->Router;

		$infos = [
			'DEBUGGING' => DEBUG ? 'yes' : 'no',

			'Server' => '-',
			'PHP Version' => phpversion(),
			'HTTP Version' => $r->httpVersion,
			'Addr' => $_SERVER['SERVER_ADDR'],
			'Name' => $_SERVER['SERVER_NAME'],
			'Software' => $_SERVER['SERVER_SOFTWARE'],
			'Admin' => $_SERVER['SERVER_ADMIN'],

			'Configs' => '-',

			'Basepath' => $r->basePath,
			'Allowed Hosts' => implode( ', ', $r->hosts ),

			'Settings' => '-',

			'MaxExecutionTime' => ini_get( 'max_execution_time' ),
			'MemoryLimit' => ini_get( 'memory_limit' ),
			'MaxFileUploads' => ini_get( 'max_file_uploads' ),
			'UploadMaxFilesize' => ini_get( 'upload_max_filesize' ),
			'PostMaxSize' => ini_get( 'post_max_size' ),
			'MaxInputTime' => ini_get( 'max_input_time' ),

			'Extensions' => '-',

			'Core' => $this->checkExt( 'Core' ),
			'Date' => $this->checkExt( 'Date' ),
			'Json' => $this->checkExt( 'json' ),
			'Session' => $this->checkExt( 'session' ),
			'PDO' => $this->checkExt( 'pdo_mysql' ),
			'Zip' => $this->checkExt( 'zip' )
		];

		$nInfos = [];

		foreach ( $infos as $k => $v )
			$nInfos[] = [$k, $v];

		return [
			'title' => $this->lang->devTitle,
			'nonce' => $this->nonce(),
			'infos' => $nInfos,
			'debug' => DEBUG
		];

	}

	public function onAjax( AjaxRequest $req ) {

		if ( !$this->checkNonce( $req ) )
			return;

		if ( !$this->isAdmin() )
			return $req->errorAuth();

		$action = (string) ( $req->data->action ?? '' );

		switch ( $action ) {

			case 'clear':
				$this->clearTmp();
				break;

			case 'debug':
				$this->toggleDebug();
				break;

			default:
				return $req->error( 'noaction found' );

		}

		$req->ok( 'Success' );

	}

	// PROTECTED
	protected function checkExt( string $ext ) {
		return extension_loaded( $ext ) ? 'yes' : 'no';
	}

	protected function clearTmp() {
		if ( is_dir( TMP_PATH ) )
			FS::removeRecursive( TMP_PATH );
	}

	protected function toggleDebug() {
		if ( DEBUG )
			unlink( DEBUG_FILE );
		else
			FS::write( DEBUG_FILE, 'Delete to go into Production!' );
	}

}