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
require_once(t3lib_extMgm::extPath("mailform")."/formTypesModel/class.tx_mailform_emailRecipient.php");

/**
* mailform module tt_content_tx_mailform_forms
*
* @to add an additional form type, register the form type in tx_mailform_form.
*
* @author       Sebastian Winterhalder <sw@internetgalerie.ch>
* 
*/
abstract class tx_mailform_formRequest extends tx_mailform_emailRecipient {
  
	/**
	 *
	 * Get Request HTML
	 *
	 */
	protected function getRequestHtml() {
		global $LANG, $BACK_PATH;
		
		$array = array();
		$array[] = $this->makeTitleRow($LANG->getLL('request_option'));
		$array[] = $this->makeRow(  $LANG->getLL('request_get_value_from_request'),
		                       $this->makeCheckbox('request_use_request'),
		                       $LANG->getLL('request_standard_value_replaced')
		                       );
		$array[] = $this->makeRow(  $LANG->getLL('request_post_name'),
		                       $this->makeInputField('request_post_name', '')
		                       );
		
		return $array;
	}
	
	/**
	 * getRequestValue()
	 *
	 * @return String
	 */
	protected function getRequestValue() {
		
		if(isset($_GET[$this->configData['request_post_name']])) {
			return htmlspecialchars($_GET[$this->configData['request_post_name']]);
		} elseif(isset($_POST[$this->configData['request_post_name']])) {
			return htmlspecialchars($_POST[$this->configData['request_post_name']]);
		} elseif(isset($_SESSION[$this->configData['request_post_name']])) {
			return htmlspecialchars($_SESSION[$this->configData['request_post_name']]);	
		}
		else {
			return '';
		}
	}

	/**
	 * useRequestValue()
	 *
	 * @return Boolean
	 */
	protected function useRequestValue() {
		if($this->configData['request_use_request'] == 'on') {
			return true;
		}
		else
			return false;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/class.tx_mailform_formRequest.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/class.tx_mailform_formRequest.php']);
}