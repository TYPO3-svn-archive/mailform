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
require_once(t3lib_extMgm::extPath("mailform")."/formTypesModel/class.tx_mailform_formMultiple.php");

/**
 * 
 * mailform module tt_content_tx_mailform_forms
 *
 * @author Sebastian Winterhalder <typo3@internetgalerie.ch>
 * @since 30.07.2008 Ueberarbeitet
 */
class tx_mailform_formRadio extends tx_mailform_formMultiple {
	protected $be_typeImage = '../gfx/type/radio.gif';
	
	/**
	 * fieldInit()
	 *
	 */
	protected function fieldInit() {
		global $FE_Handler;
		$this->hasInitialized = true;
		if(is_object($FE_Handler))
			$this->templateObj = $FE_Handler->getTemplateParser()->getTemplateObject('RADIO','form');
		}

	/**
	 * Returns ' checked' if the given index of checkboxes is checked by post
	 *
	 * @param String $value
	 * @return String
	 */
	protected function isChecked($cntInput) {
		$checked_tmp = 'checked="checked" ';
		
		if(!empty($this->postData['radio'])) {
			$selected = ($cntInput['value'] == $this->postData['radio']['value']) ? $checked_tmp : "";
		} else {
			$selected = ($this->configData['form_standard_value'] == $cntInput['value']) ? $checked_tmp : "";
		}
		
		return $selected;
	}

	/**
	 * renderFrontend()
	 * Will be inherited by Checkbox and Radio
	 * 
	 * @return String
	 */
	protected function renderFrontend() {
		global $FE_Handler;
		if(!is_object($this->templateObject)) {
			throw new Exception("Template Object not set");
		}
		
		$this->initMultiple();
		if(!is_array($this->configData['multiple_option'])) {
				$this->configData['multiple_option'] = array();
		}
		
		$wrap_tmpl = $this->templateObject->getSubElement('WRAP', 'subel');
		$tmp = $this->templateObject->getSubElement('SINGLEELEMENT', 'subel');
		$count_elements = $element = 0;
		$result = "";
		foreach($this->configData['multiple_option'] as $boxKey => $lineOption) {
			
			if(!is_array($lineOption)) {
				$lineOption['value'] = $lineOption;
				$lineOption['display'] = $lineOption;
			}
			
			$subTemplate = clone $tmp;
			$subTemplate->addOutput("FIELDID", $this->getUniqueIDName('div')."-".$element);
			$subTemplate->addOutput("FIELDVALUE", $lineOption['value']);
			$subTemplate->addOutput("FIELDNAME", $this->getUniqueFieldName()."[value]");
			$subTemplate->addOutput('SELECTED', $this->isChecked($lineOption));
			$subTemplate->addOutput('FIELDDISPLAY', $lineOption['display']);
			$subTemplate->addOutput('CLASS', $this->getUniqueIDName('class'));
	
			$fields .= $subTemplate->getParsedHtml();

			if($this->configData['build_rowcount'] <= ($count_elements+1)) {
				$count_elements = 0;
				$wrapTemplate = clone $wrap_tmpl;
				$wrapTemplate->addOutput('ELEMENTS', $fields);
				$result .= $wrapTemplate->getParsedHtml();
				$fields = "";
			} else {
				$count_elements++;
			}
			$element++;
		}
		if($count_elements != 0) {
			$wrapTemplate = clone $wrap_tmpl;
			$wrapTemplate->addOutput('ELEMENTS', $fields);
			$result .= $wrapTemplate->getParsedHtml();
		}
		// Delete SubElement Parts from Template
		$this->templateObject->addOutput('SINGLEELEMENT', '');
		$this->templateObject->addOutput('WRAP', '');
		$checkHidden = '<input type="hidden" value="1" name="'.$this->getUniqueFieldName().'[radio_sent]" />';
		$this->templateObject->addOutput('FORMHIDDEN', $checkHidden);
		
		return $this->getWithTemplate($this->configData['label'], $result);
	}
	
	/**
	 * RenderHtml()
	 * Build backend wizard element
	 *
	 * @return Array
	 */
	protected function renderHtml() {
		global $LANG;
		
		$array = array();
		$array[] = $this->makeTitleRow($LANG->getLL('form_options'));

		// Setup build horder field
		//$array[] = $this->makeRow($LANG->getLL('forms_buildOrder'), $this->makeCheckbox('build_vertical'));
		if($this->configData['build_rowcount'] == "")
			$this->configData['build_rowcount'] = $this->default_row_elements;
			
		$array[]	= $this->makeRow($LANG->getLL('forms_countTillBreak'), $this->makeInputField('build_rowcount', $this->configData['build_rowcount']));
		$array[]	= $this->makeRow($LANG->getLL('form_standard_value'), $this->makeInputField('form_standard_value', $this->configData['form_standard_value']));
		
		return array_merge($array, $this->getMultipleHtml());
	}
 
	/**
	 * enteredRequired()
	 * Inherit from tx_mailform_formAbstract
	 *
	 * @return Boolean
	 */
	public function enteredRequired() {
		if($this->isFormRequired()) {
			if($this->postData[$this->configData['type']]['value'] != '' && isset($this->postData[$this->configData['type']]['radio_sent']))
				return true;
			else return false;
		}
		else
			return true;
	}
 
	/**
	 * get Email Value
	 *
	 * @param Boolean $rawText
	 * @return String
	 */
	public function getEmailValue($rawText=false) {
		return $this->postData[$this->configData['type']]['value'];
	}
	
	/**
	 * getField Value
	 *
	 * @return String
	 */
	public function getFieldValue() {
		if(!empty($this->postData)){
			return $this->postData['radio'];
		}
		else
			return $this->configData['input_field_value'];
	}
 
	/**
	 * validVieldPost()
	 * Inherit from tx_mailform_formAbstract
	 *
	 * @return unknown
	 */
	public function validFieldPost() {
		return $this->enteredRequired($this->postData['radio']);
	}

	/**
	 * Xajax
	 *
	 * @param unknown_type $formArg
	 * @param unknown_type $pageNr
	 * @param unknown_type $fieldNr
	 * @param unknown_type $label
	 * @param unknown_type $validation_type
	 * @param unknown_type $add_regex
	 * @return Object
	 */
	public function radio_vreq($formArg, $pageNr, $fieldNr, $label, $validation_type, $add_regex) {
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
	
		//return the xajaxResponse object
		return $objResponse;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_formRadio.php']) {
 include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_formRadio.php']);
}
?>