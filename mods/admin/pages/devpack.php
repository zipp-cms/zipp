<?php
/*
@package: Zipp
@version: 0.2 <2019-07-11>
*/

namespace Admin\Pages;

use Admin\{Page, DataRequest};
use Core\FS;
use Ajax\Request as AjaxRequest;

class DevPack extends Page {

	protected $section = 'dev';

	protected $slug = 'devpack';

	protected $nonceKey = 'devpack';

	protected $template = 'dev';

	public function onData( DataRequest $req ) {

		if ( !$this->isAdmin() )
			return $l->authError;

		// $r = $this->mods->Router;

		// $cfgs = $this->mods->getConfigs();

		return [
			'title' => $this->lang->devPackTitle,
			'nonce' => $this->nonce()
		];

	}

	public function onAjax( AjaxRequest $req ) {

		if ( !$this->checkNonce( $req ) )
			return;

		if ( !$this->isAdmin() )
			return $req->errorAuth();

		$action = (string) ( $req->data->action ?? '' );

		switch ( $action ) {

			case 'backup':
				$this->backup();
				break;

			case 'pack':
				$this->pack();
				break;

			default:
				return $req->error( 'noaction found' );

		}

		$req->ok( 'Success' );

	}

	public function backup() {

		$backupPath = DIR. 'backup'. DS;
		if ( !is_dir( $backupPath ) )
			mkdir( $backupPath );

		$bFile = $backupPath. 'backup_'. date('Y-m-d_H-i-s'). '.zipp';

		$stagesFile = DIR. 'mods'. DS. 'stages.json';

		FS::zip( DIR, $bFile, [ $backupPath, TMP_PATH, $stagesFile ] );

	}

	public function pack() {

	}

}