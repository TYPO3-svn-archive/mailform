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
require_once(t3lib_extMgm::extPath("mailform")."/formTypesModel/models/class.tx_mailform_formText.php");

/**
 *
* tx_mailform_formTextwdesc
*
* @author	Sebastian Winterhalder <sw@internetgalerie.ch>
*
*/
class tx_mailform_formTextwdesc extends tx_mailform_formText {

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
			$this->configData['input_field_value'] = $this->postData['textwdesc'];
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
	 *
	 * @return Array
	 */
	protected function renderHtml() {
		global $LANG;
		$array = array();
		$array[] = $this->startRowEnv();
		$array[] = $this->makeTitleRow($LANG->getLL('form_options'));
		$array[] = $this->row_preFormDescription();
		$array[] = $this->row_preFormStandardValue();
		$array[] = $this->makeRow($LANG->getLL('form_input_cols'), $this->makeInputField('input_form_cols', $this->input_field_cols));
		$array[] = $this->endRowEnv();

		$array = array_merge($array, $this->getValidationHtml());
		$array = array_merge($array, $this->getEmailRecipientHtml());
		return $array;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_formTextwdesc.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_formTextwdesc.php']);
}
?>