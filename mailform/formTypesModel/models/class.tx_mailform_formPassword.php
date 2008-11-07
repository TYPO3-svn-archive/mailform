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
require_once(t3lib_extMgm::extPath("mailform")."/formTypesModel/class.tx_mailform_formValidation.php");

/**
 * mailform module tt_content_tx_mailform_forms
 *
 * @author Sebastian Winterhalder <typo3@internetgalerie.ch>
 */
class tx_mailform_formPassword extends tx_mailform_formValidation {

	/**
	 * Field Initialization
	 *
	 */
	protected function fieldInit() {
		$this->hasInitialized = true;
	}
	
	/**
	 * renderFrontend()
	 *
	 * @return String
	 */
	protected function renderFrontend() {
		$field = '<input class="tx_mailform_password" name="'.$this->getUniqueFieldname().'" type="password" value="'.$this->configData['input_field_value'].'" />';
		return $this->getWithTemplate($this->configData['label'], $field);
	}

	/**
	 * renderHtml()
	 *
	 * @return Array
	 */
	protected function renderHtml() {
		global $LANG;
		$array = array();
		$array[] = $this->startRowEnv();
		$array[] = $this->makeTitleRow($LANG->getLL('form_options'));
		$array[] = $this->makeRow($LANG->getLL('form_standard_value'), $this->makeInputField('form_standard_value', $this->form_standard_value));
		$array[] = $this->makeRow($LANG->getLL('form_input_cols'), $this->makeInputField('form_input_cols', $this->form_input_cols, 5));
		$array[] = $this->endRowEnv();
		
		$array = array_merge($array, $this->getValidationHtml());
		return $array;
	}

	/**
	 * Inherit from tx_mailform_formAbstract
	 *
	 * @return Boolean
	 */
	public function enteredRequired() {
		if($this->isFormRequired()) {
			if($this->postVarSet()) {
				return (strlen($this->postData[$this->configData['type']]) > 0);
			} else return false;
		} else return true;
	}
	
	/**
	 * validateField
	 * Inherit from tx_mailform_formAbstract
	 *
	 */
	public function validateField() {
		// Make sure the field is not twice validated
		if($this->alreadyValidated)
			return true;
		parent::validateField();
		
		if(isset($this->configData['validation_type'])) {
			$arr = $this->isValidWithType($this->postData[$this->configData['type']], $this->configData['validation_type'], $this->configData['validation_regex_input']);
			$this->appendFormDataValid($arr[0], $arr[1]);
		} else
			$this->appendFormDataValid(false, "No Validation Required");
	}
 
	/*
	 * getFieldValue
	 * 
	 */
	public function getFieldValue() {
		if(!empty($this->postData))
			return $this->postData['password'];
		else
			return $this->configData['input_field_value'];
	}

	/**
	 * validFieldPost()
	 *
	 * @return Boolean
	 */
	public function validFieldPost() {
		if(!empty($this->postData)) {
				$val = $this->isValidWithType($this->postData['password'], $this->configData['validation_type'], $this->configData['validation_regex_input']);
			if($val[0] && $this->enteredRequired($this->postData['password'])) {
				return true;
			} else {
				return false;
			}
		} else return false;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_formPassword.php']) {
 include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_formPassword.php']);
}
?>