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
class tx_mailform_formTextarea extends tx_mailform_formRequest {

	protected $input_field_rows = 5;
	protected $form_standard_value = "";
	protected $be_typeImage = '../gfx/type/textarea.gif';

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
		if(!empty($this->postData['textarea']) || (isset($this->postData[$this->configData['type']]) && $this->postData[$this->configData['type']] != $this->configData['form_standard_value'])) {
			$this->configData['input_field_value'] = $this->postData['textarea'];
		} else {
			if(!empty($this->configData['form_standard_value'])) {
				$this->configData['input_field_value'] = $this->configData['form_standard_value'];
			}
		}

		if($this->xajaxEnabled())
			$xajax = 'xajax_tx_mailform_textarea_vreq(xajax.getFormValues(\'tx_mailform_'.$this->cObj->data['uid'].'\'), '.$this->pageNr.', '.$this->fieldNr.', \''.$this->configData['label'].'\', \''.$this->configData['validation_type'].'\', \''.$this->configData['validation_regex_input'].'\');';

		$this->templateObject->addOutput('FORMROWS', $this->configData['form_input_rows']);
		$this->templateObject->addOutput('FORMCOLS', $this->configData['form_input_cols']);
		$this->templateObject->addOutput('FORMVALUE', $this->configData['input_field_value']);
		$this->templateObject->addOutput('FORMCLASS', 'tx_mailform_textarea');
		$this->templateObject->addOutput('FORMNAME', $this->getUniqueFieldname());
		$this->templateObject->addOutput('FORMID', $this->getUniqueIDName("input"));
		$this->templateObject->addOutput('ADDATTRIBUTES', $xajax);

		return $this->getWithTemplate($this->configData['label'], $content, $this->isFormRequired(), -1);
	}

	/**
	 * renderHtml();
	 *
	 * @return Array
	 */
	protected function renderHtml() {
		global $LANG;

		$array = array();
		$array[] = $this->startRowEnv();
		$array[] = $this->makeTitleRow($LANG->getLL('form_option'));
		$array[] = $this->makeRow($LANG->getLL('form_standard_value'), $this->makeTextarea('form_standard_value', $this->form_standard_value, 30, 4));
		$array[] = $this->makeRow($LANG->getLL('form_input_cols'), $this->makeInputField('form_input_cols', $this->form_input_cols));
		$array[] = $this->makeRow($LANG->getLL('form_input_rows'), $this->makeInputField('form_input_rows', $this->input_field_rows));
		$array[] = $this->endRowEnv();
		return array_merge($array, $this->getRequestHtml());
	}

	/**
	 * enteredRequired().
	 * Inherit from tx_mailform_formAbstract
	 *
	 * @return Boolean
	 */
	public function enteredRequired() {
		if($this->isFormRequired()) {
			return (	(strlen($this->postData[$this->configData['type']]) > 0 && empty($this->configData['form_standard_value']))
						|| (!empty($this->configData['form_standard_value']) && ($this->postData[$this->configData['type']] != $this->configData['form_standard_value'])) );
		} else return true;
	}

	/**
	 * savePost().
	 * Inherit from tx_mailform_formAbstract
	 *
	 */
	public function savePost($mailid) {
		return $this->dbHan_saveField($mailid, '', $this->postData[$this->configData['type']], '', '');
	}

	/**
	 * getFieldValue()
	 *
	 * @return String
	 */
	public function getFieldValue() {
		if(!empty($this->postData))
			return $this->postData['textarea'];
		else
			return $this->configData['input_field_value'];
		}

	/**
	 * validFieldPost().
	 * Inherit from tx_mailform_formAbstract
	 *
	 * @return Boolean
	 */
	public function validFieldPost() {
		return true;
	}

	/**
	 * get Email Value // Overwrite parent Abstract
	 *
	 * @param Boolean $rawText
	 * @return String
	 */
	public function getEmailValue($rawText=false) {
		if($rawText) {
			$result = str_replace("\n", "\n", $this->postData[$this->configData['type']]);
		} else {
			$result = str_replace("\n", "<br>", $this->postData[$this->configData['type']]);
		}
		return $result;
	}
	
	/**
	 * textarea_vreq($formArg, $pageNr, $fieldNr, $label, $validation_type, $add_regex)
	 *
	 * @param String $formArg
	 * @param Integer $pageNr
	 * @param Integer $fieldNr
	 * @param String $label
	 * @param String $validation_type
	 * @param String $add_regex
	 * @return Object
	 */
	public function textarea_vreq($formArg, $pageNr, $fieldNr, $label, $validation_type, $add_regex) {
		global $LANG;

		//tx_mailform_xajaxHandler::getInstance()->getXajaxObject()->getFormValues('formId');
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
		if($this->isFormRequired() && !$this->enteredRequired($fieldValue)) {
			$objResponse->addAssign($this->getUniqueIDName("td-inner", $pageNr, $fieldNr),"innerHTML",$this->getStateImg(true, -1));
			$objResponse->addAssign($this->getUniqueIDName("div", $pageNr, $fieldNr),"className", "tx_mailform_required");
		} elseif($this->isFormRequired()) {
			$objResponse->addAssign($this->getUniqueIDName("td-inner", $pageNr, $fieldNr),"innerHTML",$this->getStateImg(true, true));
			$objResponse->addAssign($this->getUniqueIDName("div", $pageNr, $fieldNr),"className", "tx_mailform_valid");
		} else {
			$objResponse->addAssign($this->getUniqueIDName("td-inner", $pageNr, $fieldNr),"innerHTML",$this->getStateImg(false, -1));
			$objResponse->addAssign($this->getUniqueIDName("div", $pageNr, $fieldNr),"className", "tx_mailform_none");
		}

		//return the  xajaxResponse object
		return $objResponse;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_formTextarea.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_formTextarea.php']);
}