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
require_once(t3lib_extMgm::extPath("mailform")."/formTypesModel/class.tx_mailform_layoutAbstract.php");

/**
 *
* tx_mailform_layoutHtmlelement
*
* @author	Sebastian Winterhalder <sw@internetgalerie.ch>
*
*/
class tx_mailform_layoutError extends tx_mailform_layoutAbstract {

	protected $be_typeImage = '../gfx/type/error.gif';
	protected $labelField = true;
	protected $requireBox = true; // Display require box
	protected $error_display_always = false;
	
	/**
	 * Initialization
	 *
	 */
	protected function fieldInit() {
		$this->requireBox = false;
		$this->hasInitialized = true;
	}

	/**
	 * renderFrontend()
	 *
	 * @return String
	 */
	protected function renderFrontend() {
		global $FE_Handler;

		if(!empty($this->configData['error_display_always']) || !empty($_POST) ) {
			$result = '';
			$fieldElements = $FE_Handler->getFieldElements();
			if(!empty($this->configData['error_display_pageerror'])) {
				foreach($fieldElements as $element) {
					foreach($element as $formElement) {
						if(!$formElement->validField() && $FE_Handler->isFormInDisplay($formElement->getForm()->getUFID())) {
							$tmp = $FE_Handler->getTemplateParser()->getTemplateObject('ERROR', 'layout')->getSubElement('ERRORLIST', 'subel');
							$subTemplate = clone $tmp;
							$subTemplate->addOutput("FIELDLABEL", $formElement->getForm()->getLabel());
							$subTemplate->addOutput("FIELDERRORMSG", $formElement->getForm()->getStateMessage(true));
							
							$display = $this->configData['error_hide_images'] == "on" ? false : true;
							$subTemplate->addOutput("FIELDERRORIMG", $formElement->getForm()->getStateImg($display));
							$result .= $subTemplate->getParsedHtml();
						}
					}
				}
			} else {
				$fields = $FE_Handler->getTableFields();
				foreach($fields[$FE_Handler->getCurrentPage()] as $row) {
					foreach($row as $element) {
						$array = $element->getFormElements();
						foreach($array as $formElement) {
							if(!$formElement->validField() && $FE_Handler->isFormInDisplay($formElement->getForm()->getUFID())) {
								$tmp = $FE_Handler->getTemplateParser()->getTemplateObject('ERROR', 'layout')->getSubElement('ERRORLIST', 'subel');
								$subTemplate = clone $tmp;
								$subTemplate->addOutput("FIELDLABEL", $formElement->getForm()->getLabel());
								$subTemplate->addOutput("FIELDERRORMSG", $formElement->getForm()->getStateMessage());

								$display = $this->configData['error_hide_images'] == "on" ? false : true;
								$subTemplate->addOutput("FIELDERRORIMG", $formElement->getForm()->getStateImg($display));
								$result .= $subTemplate->getParsedHtml();
							}
						}
					}
				}
				
			}
			
			/*
			foreach($fieldElements[$FE_Handler->getCurrentPage()] as $formElement) {
				if(!$formElement->getForm()->validFieldPost() && $FE_Handler->isFormInDisplay($formElement->getForm()->getUFID())) {
					$tmp = $FE_Handler->getTemplateParser()->getTemplateObject('ERROR', 'layout')->getSubElement('ERRORLIST', 'subel');
					$subTemplate = clone $tmp;
					$subTemplate->addOutput("FIELDLABEL", $formElement->getForm()->getLabel());
					$subTemplate->addOutput("FIELDERRORMSG", $formElement->getForm()->getStateMessage(true));
					
					$display = $this->configData['error_hide_images'] == "on" ? false : true;
					$subTemplate->addOutput("FIELDERRORIMG", $formElement->getForm()->getStateImg($display));
					$result .= $subTemplate->getParsedHtml();
				}
			}
*/
			$this->templateObject->addOutput('ERRORLIST', $result);
			
			
			return $this->getWithTemplate($this->configData['label'], '');
		} else
			return '';
	}
	
	
	
	/**
	 * renderHtml()
	 *
	 * @return Array
	 */
	protected function renderHtml() {
		global $LANG;
		
		$array = array();
		$array[] = $this->makeRow($LANG->getLL('forms_error_displayAlways'), $this->makeCheckbox('error_display_always', $this->error_display_always));
		$array[] = $this->makeRow($LANG->getLL('forms_error_displayPageError'), $this->makeCheckbox('error_display_pageerror', $this->configData['error_display_pageerror']));
		
		$array[] = $this->makeRow($LANG->getLL('forms_error_hideImages'), $this->makeCheckbox('error_hide_images', $this->error_hide_images));
		
		return $array;
	}

	/**
	 * getFieldValue()
	 *
	 * @return String
	 */
  	public function getFieldValue() { return ""; }

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
	 * getEmailValue($rawText).
	 * Inherit from tx_mailform_formAbstract
	 *
	 * @return String
	 */
	public function getEmailValue($rawText) {
		return '';
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_layoutError.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_layoutError.php']);
}
?>