<?php
/*
@package: Zipp
@version: 0.2 <2021-01-30>
*/

namespace Example;

use Themes\Theme;
use Fields\Validator;

class Example extends Theme {

	protected $dependencies = [ 'fields', 'mediaint', 'pagesint' ];

	public function onInit() {
		$this->loadConfig();
	}

	public function handleContact() {

		// how to get settings fields
		// activeData
		$activeData = $this->activeData;
		$post = $this->activeRequest->post;

		$name = (string) ( $post->name ?? '' );
		$email = (string) ( $post->email ?? '' );
		$content = (string) ( $post->content ?? '' );
		$success = false;

		$success = Validator::email( $email, true ) && Validator::str( $name, true ) && Validator::str( $content, true, 10 );

		if ( $success )
			$this->sendMail( $activeData, $name, $email, $content );

		return (object) [
			'success' => $success,
			'name' => $name,
			'email' => $email,
			'content' => $content
		];

	}

	public function sendMail( object $data, string $name, string $email, string $content ) {

		$settings = $data->main;
		$ctn = $data->ctn;

		$to = $settings->contactFormEmail;
		$subject = $ctn->subject;

		$header  = "MIME-Version: 1.0\r\n";
		$header .= "Content-type: text/html; charset=utf-8\r\n";
		$header .= sprintf( "From: mailer@%s\r\n", $this->activeRequest->host );
		$header .= sprintf( "Reply-To: %s\r\n", $to );

		$str = sprintf('<b>%s</b>:<br>%s<br>', e( $ctn->name ), e( $name ) );
		$str .= sprintf('<b>%s</b>:<br>%s<br>', e( $ctn->email ), e( $email ) );
		$str .= sprintf('<b>%s</b>:<br>%s<br>', e( $ctn->content ), e( $content ) );

		mail( $to, $subject, $str, $header );
	}

}