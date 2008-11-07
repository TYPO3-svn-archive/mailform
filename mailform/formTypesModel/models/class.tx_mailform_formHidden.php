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
 * @author       Sebastian Winterhalder <typo3@internetgalerie.ch>
 */
class tx_mailform_formHidden extends tx_mailform_formRequest {

	protected $requireBox = false; // Set the required box false

	/**
	 * fieldInit()
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
		require_once(t3lib_extMgm::extPath('mailform')."lib/layout/form/class.tx_mailform_input.php");
		$hidden_field = new tx_mailform_input();
		$hidden_field->setId($this->getUniqueIDName("input"));
		$hidden_field->setName($this->getUniqueFieldName());
		$hidden_field->setType('hidden');
		
		if($this->useRequestValue())
			$hidden_field->setValue($this->getRequestValue());
		else
			$hidden_field->setValue($this->configData['input_field_value']);
		
		return $this->getWithTemplate('', $hidden_field->getElementRendered());
	}

	/**
	 *
	 * Backend Code
	 *
	 **/
	protected function renderHtml() {
		global $LANG;
		$array = array();
		$array[] = $this->makeTitleRow($LANG->getLL('form_options'));
		$array[] = $this->makeRow($LANG->getLL('form_field_value'), $this->makeInputField('input_field_value', $this->input_field_value));
		$array = array_merge($array, $this->getRequestHtml());
		return $array;
	}
  
	/**
	 * enteredRequired()
	 * Inherit from tx_mailform_formAbstract
	 *
	 * @return Boolean
	 */
	public function enteredRequired() { return true; }

	/**
	 * getFieldValue()
	 * Inherit from tx_mailform_formAbstract
	 *
	 * @return String
	 */
	public function getFieldValue() {
		if(!empty($this->postData))
			return $this->postData['hidden'];
		else
			return $this->configData['input_field_value'];
	}
  
	/**
	 * validFieldPost()
	 * Inherit from tx_mailform_formAbstract
	 * 
	 * @return Boolean
	 */
	public function validFieldPost() { return true; }

}
  
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_formHidden.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_formHidden.php']);
}
?>