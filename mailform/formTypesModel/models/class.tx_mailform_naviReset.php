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
require_once(t3lib_extMgm::extPath("mailform")."/formTypesModel/class.tx_mailform_naviAbstract.php");

/**
 *
* tx_mailform_layoutHtmlelement
*
* @author	Sebastian Winterhalder <sw@internetgalerie.ch>
*
*/
class tx_mailform_naviReset extends tx_mailform_naviAbstract {

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
		$input = '
			<input type="submit" value="Reset" name="'.tx_mailform_naviAbstract::getVarPrefix().'[reset][0]">
			<input type="hidden" value="1" name="'.tx_mailform_naviAbstract::getVarPrefix().'[reset][1]" />
			';
		
		return $this->getWithTemplate($this->configData['label'], $input);
	}

	/**
	 * renderHtml()
	 *
	 * @return Array
	 */
	protected function renderHtml() {
		global $LANG;
		$arr = array();
		$arr[] = $this->makeTitleRow($LANG->getLL('form_options'));

		return $arr;
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
		if(!empty($this->configData['display_field_on_email']))
			return false;

		if(!$rawText) {
			return $this->configData['input_html_value'];
		} else {
			return htmlspecialchars($this->configData['input_html_value']);
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_naviReset.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_naviReset.php']);
}