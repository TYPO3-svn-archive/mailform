<?php
/***************************************************************
*Copyright notice
*
*(c) 2007 Sebastian Winterhalder <sw@internetgalerie.ch>
*All rights reserved
*
*This script is part of the TYPO3 project. The TYPO3 project is
*free software; you can redistribute it and/or modify
*it under the terms of the GNU General Public License as published by
*the Free Software Foundation; either version 2 of the License, or
*(at your option) any later version.
*
*The GNU General Public License can be found at
*http://www.gnu.org/copyleft/gpl.html.
*
*This script is distributed in the hope that it will be useful,
*but WITHOUT ANY WARRANTY; without even the implied warranty of
*MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
*GNU General Public License for more details.
*
*This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
require_once(t3lib_extMgm::extPath("mailform")."/formTypesModel/class.tx_mailform_formAbstract.php");

/**
* mailform module tt_content_tx_mailform_forms
*
* @author Sebastian Winterhalder <typo3@internetgalerie.ch>
*/
class tx_mailform_formDefault extends tx_mailform_formAbstract {

	protected $requireBox = false; // Display require box
	protected $labelField = false; // Use Label in Configuration
	protected $singleUse = false; // Inherited form type can only once be included in fieldconfig
	protected $displayError = false; // Displays or Hides the FE-User Error (validation)
	
	/**
	 * Initialization
	 *
	 */
	protected function fieldInit() {
		$this->hasInitialized = true;
	}

	/**
	 *
	 * Frontend Code
	 *
	 */
	protected function renderFrontend() {
		return "<div>Default Field - This field is misconfigured</div>";
	}

	/**
	 *
	 * Backend Code
	 *
	 **/

	/**
	 * This form is only for choosing, so the form content is nothing
	 *
	 * @return Array
	 */
	protected function renderHtml() {
		return array();
	}

	/**
	 * // Inherit from tx_mailform_formAbstract, and dont care if content valid
	 *
	 * @return Boolean
	 */
	public function enteredRequired() {return true;}

	/**
	 * getFieldValue()
	 *
	 * @return String
	 */
	public function getFieldValue() {
		if(!empty($this->postData))
			return $this->postData['default'];
		else
			return $this->configData['input_field_value'];
	}
 
	/**
	 * // Inherit from tx_mailform_formAbstract
	 *
	 * @return Boolean
	 */
	public function validFieldPost() {return true;}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_formDefault.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_formDefault.php']);
}
?>