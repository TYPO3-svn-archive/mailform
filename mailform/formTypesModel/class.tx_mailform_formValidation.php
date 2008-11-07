<?php
/***************************************************************
* Copyright notice
*
* (c) 2007 Sebastian Winterhalder <sw@internetgalerie.ch>
* All rights reserved
*
* This script is part of the TYPO3 project. The TYPO3 project is
* free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
require_once(t3lib_extMgm::extPath("mailform")."/formTypesModel/class.tx_mailform_formAbstract.php");

/**
* mailform module tt_content_tx_mailform_forms
*
* @to add an additional form type, register the form type in tx_mailform_form.
*
* @author Sebastian Winterhalder <sw@internetgalerie.ch>
* 
*/
abstract class tx_mailform_formValidation extends tx_mailform_formAbstract {
 
	private $validationTypes = array('no_validation', 'mail', 'alphanum', 'numbers', 'letters', 'regex_input', 'value_check');
	public $errorMessage;
	
	/**
	 *
	 * Get Validation HTML
	 *
	 */ 
	protected function getValidationHtml() {
		global $LANG;
	
		foreach($this->validationTypes as $types) {
			$array[$types] = $LANG->getLL('validation_'.$types);
		}
	 
		$res = array();
		$res[] = $this->makeTitleRow($LANG->getLL('validation_validation'));
	
		$res[] = $this->makeRow($LANG->getLL('validation_type'), $this->makeSelectBox('validation_type', $array, $this->configData['validation_type'],1, 'this.form.submit()'));
	
		switch($this->configData['validation_type']) {
			case 'value_check':
				$res[] = $this->makeRow($LANG->getLL('validation_value'), $this->makeInputField('validation_regex_input', ''));
			break;
			case 'regex_input':
				$res[] = $this->makeRow($LANG->getLL('validation_regex_input'), $this->makeInputField('validation_regex_input', ''));
			break;
			default: break;
		}
	
		if(empty($this->configData['validation_error_message'])) {
			$this->configData['validation_error_message'] = $LANG->getLL('validation_err_standard_msg');
		}
	 
		$res[] = $this->makeRow($LANG->getLL('validation_error_message'), $this->makeInputField('validation_error_message', $this->configData['validation_error_message']));
		$res[] = $this->makeRow($LANG->getLL('validation_allow_empty'), $this->makeCheckbox("validation_allow_empty"), $LANG->getLL('validation_allow_empty_desc'));
	
		return $res;
	}

	/**
	 * isValidInput()
	 *
	 * return Boolean
	 */
	protected function isValidInput() {
		return $this->isValidWithType($this->configData['input_field_value'], $this->configData['validation_type'], $this->configData['validation_value_check']);
	}

	/**
	 *
	 * Normal validation types
	 *
	 *@return Boolean
	 */
	 public function isValidWithType($validationString, $method, $optionalRegex="") {
		$valid = false;
		switch($method) {
			case "mail":
				if(!$this->isFormRequired() && strlen($validationString) <= 0)
					$valid = 1;
				 else
					$valid = t3lib_div::validEmail($validationString);
				$message = $this->configData['validation_error_message'];
			break;
			case "alphanum":
				$exp = '/^[a-zA-Z0-9]+$/';
				if(!$this->isFormRequired() && strlen($validationString) <= 0)
					$valid = 1;
				else
					$valid = preg_match($exp,$validationString) ? 1:0;
				$message = $this->configData['validation_error_message'];
			break;
			case "numbers":
				if(!$this->isFormRequired() && strlen($validationString) <= 0)
					$valid = 1;
				else
					$valid = is_numeric($validationString) ? 1:0;
				$message = $this->configData['validation_error_message'];
			break;
			case "letters":
				$exp = '/^[a-zA-Z]+$/';
				if(!$this->isFormRequired() && strlen($validationString) <= 0)
					$valid = 1;
				else
					$valid = preg_match($exp,$validationString) ? 1:0;
				$message = $this->configData['validation_error_message'];
			break;
			case "regex_input":
				if(!$this->isFormRequired() && strlen($validationString) <= 0)
	 				$valid = 1;
				else
					$valid = preg_match($optionalRegex,$validationString) ? 1:0;
				$message = $this->configData['validation_error_message'];
			break;
			case 'file':
				// Should never happen, the file type is checking itself
				$valid = false;
				$message = "By file check this message should not appear. Please save again your Formular in BE and contact the administrator for this error";
			break;
			case "value_check":
				if(!$this->isFormRequired() && strlen($validationString) <= 0)
					$valid = 1;
				else
					$valid = ($validationString == $optionalRegex) ? 1:0;
	 			$message = $this->configData['validation_error_message'];
				break;
	 		default: $message = "nothing";
	 			$valid = 1;
	 	}
		return array($valid, $message);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/class.tx_mailform_formValidation.php']) {
 include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/class.tx_mailform_formValidation.php']);
}