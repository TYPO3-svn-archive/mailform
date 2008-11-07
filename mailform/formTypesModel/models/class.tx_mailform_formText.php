<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Sebastian Winterhalder <sw@internetgalerie.ch>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
require_once(t3lib_extMgm::extPath("mailform")."/formTypesModel/class.tx_mailform_formRequest.php");

/**
* mailform module tt_content_tx_mailform_forms
*
* @author	Sebastian Winterhalder <sw@internetgalerie.ch>
*
*/
class tx_mailform_formText extends tx_mailform_formRequest {

	protected $be_typeImage = '../gfx/type/field.gif';

	/**
	 * Initialization
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
		// Set Post value into field
		if(!empty($this->postData[$this->configData['type']]) || (isset($this->postData[$this->configData['type']]) && $this->configData['form_standard_value'] != $this->postData[$this->configData['type']])) {
			$this->configData['input_field_value'] = $this->postData[$this->configData['type']];
		} else {
			if($this->useRequestValue()) {
				$this->configData['input_field_value'] = $this->getRequestValue();
			} else {
				$this->configData['input_field_value'] = $this->configData['form_standard_value'];
			}
		}
		
		// Xajax Response for valid and required content
		if($this->xajaxEnabled()) {
			tx_mailform_xajaxHandler::getInstance()->registerFunction(array('tx_mailform_textfield_vreq', &$this, 'textfield_vreq'));
			$xajax = ' onchange="xajax_tx_mailform_textfield_vreq(xajax.getFormValues(\'tx_mailform_'.$this->cObj->data['uid'].'\'), '.$this->pageNr.', '.$this->fieldNr.', \''.$this->configData['label'].'\', \''.$this->configData['validation_type'].'\', \''.$this->configData['validation_regex_input'].'\');"';
		}
		else
			$xajax = '';

		$this->templateObject->addOutput('ADDATTRIBUTES', $xajax);
		$this->templateObject->addOutput('FORMNAME', $this->getUniqueFieldname());
		$this->templateObject->addOutput('FORMID', $this->getUniqueIDName("input"));
		$this->templateObject->addOutput('FORMSIZE', $this->configData['input_form_cols']);
		$this->templateObject->addOutput('FORMVALUE', $this->configData['input_field_value']);
		$this->templateObject->addOutput('FORMCLASS', '');
		$field = "";
		return $this->getWithTemplate($this->configData['label'], $field, $this->isFormRequired(), -1);
	}

	/**
	 * renderHtml()
	 * Inherit from tx_mailform_formAbstract
	 *
	 * @return Array
	 */
	protected function renderHtml() {
		$array = array_merge($this->getAdditionalFields(), $this->getValidationHtml());
		$array = array_merge($array,$this->getEmailRecipientHtml());
		$array = array_merge($array,$this->getRequestHtml());
		return $array;
	}

	/**
	 * getAdditionalFields()
	 *
	 * @return Array
	 */
	private function getAdditionalFields() {
		global $LANG;
		$array[] = $this->makeTitleRow($LANG->getLL('form_options'));
		$array[] = $this->makeRow($LANG->getLL('form_standard_value'), $this->makeInputField('form_standard_value', $this->configData['form_standard_value']));
		$array[] = $this->makeRow($LANG->getLL('form_input_cols'), $this->makeInputField('input_form_cols', $this->input_field_cols));
		return $array;
	}

	/**
	 * enteredRequired()
	 * Inherit from tx_mailform_formAbstract
	 *
	 * @return Boolean
	 */
	public function enteredRequired() {
		if($this->isFormRequired()) {
			if($this->postVarSet()) {
				if(strlen($this->configData['form_standard_value']) > 0)
					return ($this->postData[$this->configData['type']] != $this->configData['form_standard_value']);
				else
					return (strlen($this->postData[$this->configData['type']]) > 0);
			} else return false;
		} else return true;
	}

	/**
	 * validateField()
	 * Inherit from tx_mailform_formAbstract
	 *
	 * @return Boolean
	 */
	public function validateField() {
		if(!$this->alreadyValidated) {
			parent::validateField();
			if(isset($this->configData['validation_type'])) {
				$arr = $this->isValidWithType($this->postData[$this->configData['type']], $this->configData['validation_type'], $this->configData['validation_regex_input']);
				
				$this->appendFormDataValid($arr[0], $arr[1]);
			} else
			{
				$this->appendFormDataValid(false, "OMG") ;
			}
		}
	}

	/**
	 * getFieldValue()
	 *
	 * @return String
	 */
	public function getFieldValue() {
		if(!empty($this->postData)) {
			return $this->postData[$this->configData['type']];
		}
		else {
			return $this->configData['input_field_value'];
		}
	}

	/**
	 * validFieldPost()
	 *
	 * @return Boolean
	 */
	public function validFieldPost() {
		if(!empty($this->postData)) {
			$val = $this->isValidWithType($this->postData[$this->configData['type']], $this->configData['validation_type'], $this->configData['validation_regex_input']);
			if($val[0]) {
				return true;
			} else {
				if(strlen($this->postData[$this->configData['type']]) <= 0 && $this->configData['validation_allow_empty'] == "on") {
					return true;
				} else {
					return false;
				}
			}
		}  else {
			if($this->configData['validation_allow_empty'] == "on")
				return true;
			else
				return false;
		}
	}

	/**
	 * XAJAX
	 * textfield_vreq()
	 *
	 * @param String $formArg
	 * @param Integer $pageNr
	 * @param Integer $fieldNr
	 * @param String $label
	 * @param String $validation_type
	 * @param String $add_regex
	 * @return Obj
	 */
	public function textfield_vreq($formArg, $pageNr, $fieldNr, $label, $validation_type, $add_regex) {
		global $LANG;

		// Instantiate the xajaxResponse object
		$objResponse = new tx_xajax_response();

		$string = $this->getUniqueFieldName("div", $pageNr, $fieldNr);
		foreach($formArg as $key => $value) {
			$string .= "[$key]=>$value:";
			foreach($value as $subKey => $val2) {
				$string .= "[$subKey]=>$val2:";
			}
		}

		$fieldValue = $formArg[$this->formPrefix][$pageNr][$fieldNr][$this->configData['type']];
		$validArr = $this->isValidWithType($fieldValue, $validation_type, $add_regex);

		if($this->isFormRequired() && !$this->enteredRequired($fieldValue)) {
			$objResponse->addAssign($this->getUniqueIDName("td-inner", $pageNr, $fieldNr),"innerHTML",$this->getStateImg(true, -1));
			$objResponse->addAssign($this->getUniqueIDName("div", $pageNr, $fieldNr),"className", "tx_mailform_required");
		} elseif($validArr[0]) {
			$objResponse->addAssign($this->getUniqueIDName("td-inner", $pageNr, $fieldNr),"innerHTML",$this->getStateImg(true, true));
			$objResponse->addAssign($this->getUniqueIDName("div", $pageNr, $fieldNr),"className", "tx_mailform_valid");
		} else {
			$objResponse->addAssign($this->getUniqueIDName("div", $pageNr, $fieldNr),"className", "tx_mailform_invalid");
			$objResponse->addAssign($this->getUniqueIDName("td-inner", $pageNr, $fieldNr),"innerHTML", $this->getStateImg(true, false));
		}
		//return the  xajaxResponse object
		return $objResponse;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_formText.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_formText.php']);
}
?>