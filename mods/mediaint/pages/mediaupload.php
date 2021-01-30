<?php
/*
@package: Zipp
@version: 0.1 <2019-06-24>
*/

namespace MediaInt\Pages;

use Ajax\Request as AjaxRequest;
use Admin\Page;
use Admin\DataRequest;
use Media\Media;

class MediaUpload extends Page {

	protected $nonceKey = 'mediaupload';

	public function onData( DataRequest $req ) {

		// max upload
		// max file
		// allowed Filetypes

		return [
			'title' => $this->lang->uploadTitle,
			'mainLang' => '',
			'nonce' => $this->nonce(),
			'uploadInfo' => $this->mod->uploadInfoText(),
			'notAllowed' => $this->lang->notAllowed,
			'text' => sprintf( $this->lang->uploadText, $this->mod->maxFileUploads, $this->mod->maxFileSize, $this->mod->maxPostSize ),
			'allowed' => Media::getAllowedExtensions()
		];

	}

}