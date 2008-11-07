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
* tx_mailform_layoutSeparator
*
* @author	Sebastian Winterhalder <sw@internetgalerie.ch>
*
*/
class tx_mailform_layoutSeparator extends tx_mailform_layoutAbstract {

	protected $be_typeImage = '../gfx/type/separator.gif';

	/**
	 * Initialization
	 *
	 */
	protected function fieldInit() {
		$this->hasInitialized = true;
		$this->requireBox = false;
	}

	/**
	 * renderFrontend()
	 *
	 * @return String
	 */
	protected function renderFrontend() {
		$fields =  '<hr class="tx_mailform_hrSeparator" />';

		return $this->getWithTemplate($this->configData['label'], $fields);
	}

	/**
	 * renderHtml()
	 *
	 * @return Array
	 */
	protected function renderHtml() {
		$array = array();
		$array[] = $this->row_preExcludeFromStats(true);
		//$array[] = $this->displayOptions();
		return $array;
	}

	/**
	 * getFieldValue()
	 *
	 * @return String
	 */
	public function getFieldValue() {
		return "";
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
	 * getEmailValue($rawText).
	 * Inherit from tx_mailform_formAbstract
	 *
	 * @return String
	 */
	public function getEmailValue($rawText) {
		if(!$rawText) {
			if(empty($this->configData['display_field_on_email']))
				return '<hr width="100%">';
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_layoutSeparator.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_layoutSeparator.php']);
}
?>