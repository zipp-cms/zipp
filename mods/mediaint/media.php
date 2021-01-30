<?php
/*
@package: Zipp
@version: 0.1 <2019-06-11>
*/

namespace MediaInt;

use Core\Module;
use Langs\Module as LangsModule;
use Fields\Module as FieldsModule;
// use Ajax\Module as AjaxModule;
use \Error;

class Media extends Module {

	use LangsModule, FieldsModule;

	// GETTERS
	public function _getMaxFileUploads() {
		return (int) ini_get( 'max_file_uploads' );
	}

	public function _getMaxFileSize() {
		return ini_get( 'upload_max_filesize' );
	}

	public function _getMaxPostSize() {
		return ini_get( 'post_max_size' );
	}

	public function uploadInfoText() {
		return sprintf( $this->lang->uploadInfoText, $this->maxFileSize );
	}

	// INIT
	public function onInit() {

		if ( $this->mods->has( 'CLI' ) )
			$cli = new SetupCLI( $this );

		if ( $this->mods->has( 'Admin' ) )
			$admin = new SetupAdmin( $this );

		if ( $this->mods->has( 'Ajax' ) )
			$ajax = new SetupAjax( $this );

		$this->addFields( ['SingleFile', 'MultipleFiles'] );

	}

}