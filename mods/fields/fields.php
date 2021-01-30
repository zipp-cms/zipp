<?php
/*
@package: Zipp
@version: 1.0 <2019-06-01>
*/

namespace Fields;

use Core\Module;
use Router\Module as RouterModule;
use \Error;

class Fields extends Module {

	use RouterModule;

	protected $fields = [];

	// GETTERS
	public function _getStyleFile() {
		return ['css/style', 'mgcss'];
	}

	public function _getScripts() {
		return [
			'js/fields',
			'js/field',
			'js/fields/text',
			'js/fields/number',
			'js/fields/textarea',
			'js/fields/hidden',
			'js/fields/editor',
			'js/fields/email',
			'js/fields/password',
			'js/fields/checkbox',
			'js/fields/keywords',
			'js/fields/dropdown',
			'js/fields/singlerepeat',
			'js/fields/multiplerepeat'
		];
	}

	// METHODS

	public function addField( string $key, string $cls ) {
		$this->fields[lower($key)] = $cls;
	}

	public function getField( string $key ) {
		return $this->fields[lower($key)] ?? null;
	}

	// INIT
	public function onInit() {

		$fields = [ 'CheckBox', 'DropDown', 'Editor', 'Email', 'Password', 'Hidden', 'Keywords', 'Number', 'SingleRepeat', 'Text', 'Textarea', 'MultipleRepeat' ];
		foreach ( $fields as $field )
			$this->addField( $field, $this->cls( 'Fields\\'. $field ) );

	}

}