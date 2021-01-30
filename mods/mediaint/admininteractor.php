<?php
/*
@package: Zipp
@version: 0.1 <2019-06-11>
*/

namespace MediaInt;

use Admin\Interactor;

class AdminInteractor extends Interactor {

	protected function onInit() {

		$l = $this->lang;

		$this->addScripts( ['mediaupload', 'popups', 'singlefile', 'multiplefiles', 'pages', 'main'] );
		$this->addStyle( 'style', 'mgcss' );

		$this->addSection( 'media', $l->mediaSection, -30 );

		$this->addPage( 'Pages\MediaUpload' );
		$this->addPageUrl( 'media/upload/', 'mediaupload' );

		$this->addPage( 'Pages\MediaEdit' );
		$this->addPageUrl( 'media/edit/', 'mediaedit' );

		$this->addPage( 'Pages\MediaSelect' );
		$this->addPageUrl( 'media/select/', 'mediaselect' );

		$this->addPage( 'Pages\DelMedia' );
		$this->addPageUrl( 'media/delmedia/', 'delmedia' );

		$this->addMultiple([
			[ 'Pages\Media', 'media', $l->mediaTitle ]
		]);

	}

}