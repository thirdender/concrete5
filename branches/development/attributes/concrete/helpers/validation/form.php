<?
/**
 * @package Helpers
 * @subpackage Validation
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Helper functions to use with validating submitting forms.
 * @package Helpers
 * @subpackage Validation
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
 
 
	defined('C5_EXECUTE') or die(_("Access Denied."));
	class ValidationFormHelper {	
		
		private $fields = array();
		private $fieldsInvalid = array();
		private $data = array();
		private $files = array();
		private $error;
		
		/**
		 * @access private
		 */
		const VALID_NOT_EMPTY = 1;
		const VALID_EMAIL = 2;
		const VALID_INTEGER = 3;
		const VALID_INTEGER_REQUIRED = 4;
		const VALID_UPLOADED_IMAGE = 10;
		const VALID_UPLOADED_IMAGE_REQUIRED = 11;
		const VALID_UPLOADED_FILE = 20;
		const VALID_UPLOADED_FILE_REQUIRED = 25;

		public function __construct() {
			$this->error = Loader::helper('validation/error');
		}
		
		
		/** 
		 * Adds a test to a field to ensure that, if set, it is a valid uploaded image.
		 * @param string $field
		 * @param string $errorMsg
		 * @param bool $emptyIsOk Tells whether this can be submitted as empty (e.g. the validation tests only run if someone is actually submitted in the post.)
		 * @return void
		 */
		public function addUploadedImage($field, $errorMsg = null, $emptyIsOk = true) {
			$const = ($emptyIsOk) ? ValidationFormHelper::VALID_UPLOADED_IMAGE : ValidationFormHelper::VALID_UPLOADED_IMAGE_REQUIRED;
			$this->addRequired($field, $errorMsg, $const);
		}
		
		/** 
		 * Adds a test to a field to ensure that, if set, it is a valid uploaded file.
		 * @param string $field
		 * @param string $errorMsg
		 * @param bool $emptyIsOk Tells whether this can be submitted as empty (e.g. the validation tests only run if someone is actually submitted in the post.)
		 * @return void
		 */
		public function addUploadedFile($field, $errorMsg = null, $emptyIsOk = true) {
			$const = ($emptyIsOk) ? ValidationFormHelper::VALID_UPLOADED_FILE : ValidationFormHelper::VALID_UPLOADED_FILE_REQUIRED;
			$this->addRequired($field, $errorMsg, $const);
		}
		
		/** 
		 * Adds a required field and tests that it is integer only
		 * @param string $field
		 * @param string $errorMsg
		 * @param bool $emptyIsOk Tells whether this can be submitted as empty (e.g. the validation tests only run if someone is actually submitted in the post.)
		 * @return void
		 */
		public function addInteger($field, $errorMsg = null, $emptyIsOk = true) {
			$const = ($emptyIsOk) ? ValidationFormHelper::VALID_INTEGER : ValidationFormHelper::VALID_INTEGER_REQUIRED;
			$this->addRequired($field, $errorMsg, ValidationFormHelper::VALID_INTEGER);
		}

		/** 
		 * Adds a required field to the form helper object. This will then be typically used in conjunction with the test() method to see
		 * if the test is passed
		 * @param string $field
		 * @param string $errorMsg
		 * @param string $validate test to validate against
		 * @return void
		 */
		public function addRequired($field, $errorMsg = null, $validate = ValidationFormHelper::VALID_NOT_EMPTY) {
			$obj = new stdClass;
			$obj->message = ($errorMsg == null) ? 'Field "' . $field . '" is invalid' : $errorMsg;
			$obj->field = $field;
			$obj->validate = $validate;
			$this->fields[] = $obj;
		}
		
		/** 
		 * Adds a required email address to the suite of tests to be run.
		 * @param string $field
		 * @param string $errorMsg
		 * @return void
		 */
		public function addRequiredEmail($field, $errorMsg = null) {
			$this->addRequired($field, $errorMsg, ValidationFormHelper::VALID_EMAIL);
		}
		
		/**
		 * Sets the data files array 
		 */
		public function setFiles() {
			$this->files = $_FILES;
		}
		
		/** 
		 * An associative array that we setup to validate against. Typical usage is $val->setData($_POST);
		 * @param array $data
		 * @return void
		 */
		public function setData($data) {
			$this->data = $data;
		}
		
		/** 
		 * @access private
		 */
		private function setErrorsFromInvalidFields() {
			foreach($this->fieldsInvalid as $f) {
				$this->error->add($f->message);	
			}
		}
		
		/** 
		 * After the validation error helper has been setup, the test() method ensures that all fields that require validation
		 * pass. Returns the number of invalid fields (0 = success)
		 * @return int
		 */
		public function test() {
			$val = Loader::helper('validation/strings');
			$num = Loader::helper('validation/numbers');
			$fil = Loader::helper('validation/file');
			
			// loop through all the fields in the array, and run whatever validation
			// the validate parameter says is required
			foreach($this->fields as $f) {
				$validate = $f->validate;
				$field = $f->field;				
				switch($validate) {
					case ValidationFormHelper::VALID_NOT_EMPTY:
						if (!$val->notempty($this->data[$field])) {
							$this->fieldsInvalid[] = $f;
						}
						break;
					case ValidationFormHelper::VALID_INTEGER:
						if ((!$num->integer($this->data[$field])) && ($val->notempty($this->data[$field]))) {
							$this->fieldsInvalid[] = $f;
						}
						break;
					case ValidationFormHelper::VALID_INTEGER_REQUIRED:
						if (!$num->integer($this->data[$field])) {
							$this->fieldsInvalid[] = $f;
						}
						break;
					case ValidationFormHelper::VALID_UPLOADED_IMAGE:
						if ((!$fil->image($this->files[$field]['tmp_name'])) && ($this->files[$field]['tmp_name'] != '')) {
							$this->fieldsInvalid[] = $f;
						}
						break;
					case ValidationFormHelper::VALID_UPLOADED_IMAGE_REQUIRED:
						if (!$fil->image($this->files[$field]['tmp_name'])) {
							$this->fieldsInvalid[] = $f;
						}
						break;
					case ValidationFormHelper::VALID_UPLOADED_FILE:
						if ((!$fil->file($this->files[$field]['tmp_name'])) && ($this->files[$field]['tmp_name'] != '')) {
							$this->fieldsInvalid[] = $f;
						}
						break;
					case ValidationFormHelper::VALID_UPLOADED_FILE_REQUIRED:
						if (!$fil->file($this->files[$field]['tmp_name'])) {
							$this->fieldsInvalid[] = $f;
						}
						break;
					case ValidationFormHelper::VALID_EMAIL:
						if (!$val->email($this->data[$field])) {
							$this->fieldsInvalid[] = $f;
						}
						break;
				}
			}
			
			$this->setErrorsFromInvalidFields();
			return count($this->fieldsInvalid) == 0;
		}
		
		/** 
		 * Gets the error object. 
		 * <code>
		 *     if ($val->test() > 0) { 
		 *         $e = $val->getError();
		 *     }
		 * </code>
		 * @return ValidationErrorHelper
		 */
		public function getError() {
			return $this->error;
		}
	
	}
	
?>