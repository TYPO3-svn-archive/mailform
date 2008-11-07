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
require_once(t3lib_extMgm::extPath("mailform")."/formTypesModel/class.tx_mailform_xajaxHandler.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/database/class.tx_mailform_db_ttContentRow.php");
if(t3lib_extMgm::isLoaded("xajax")) {
	require_once(t3lib_extMgm::extPath("xajax")."/class.tx_xajax.php");
	require_once(t3lib_extMgm::extPath("xajax")."/class.tx_xajax_response.php");
}

/**
* mailform module tt_content_tx_mailform_forms
*
* @author       Sebastian Winterhalder <sw@internetgalerie.ch>
*
*/
class tx_mailform_xajaxHandler {

	static private $instance;
	private $xajax;
	private $flexiData;

	private $functionList = array();

	/**
	 * getInstance()
	 *
	 * @return Object
	 */
	static public function getInstance() {
		if(!self::$instance) {
			self::$instance = new tx_mailform_xajaxHandler();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 *
	 */
	private function __construct() {
		if(t3lib_extMgm::isLoaded('xajax')) {
			$this->xajax = t3lib_div::makeInstance("tx_xajax");
		}
		$this->flexiData = tx_mailform_db_ttContentRow::getInstance()->getFlexformArray();
	}

	/**
	 * registerFunction($functionName)
	 *
	 * @param String $functionName
	 */
	public function registerFunction($functionName) {
		if(($this->flexiData['sDEF']['enableXajax'] == 1 && t3lib_extMgm::isLoaded('xajax')) && array_search($functionName[2], $this->functionList) === false) {
   			$this->xajax->registerFunction($functionName);
   			$this->functionList[] = $functionName[2];
		}
 	}

 	/**
 	 * getXajaxObject()
 	 *
 	 * @return Object
 	 */
	public function getXajaxObject() {
      return $this->xajax;
	}

	/**
	 * processRequests()
	 *
	 */
	public function processRequests() {
		if(t3lib_extMgm::isLoaded('xajax')) {
			$this->xajax->processRequests();
		}
	}

	/**
	 * getJavascript()
	 *
	 * @return String
	 */
	public function getJavascript() {
		if(t3lib_extMgm::isLoaded('xajax')) {
			return $this->xajax->getJavascript(t3lib_extMgm::siteRelPath('xajax'));
		}
		else return '';
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/class.tx_mailform_xajaxHandler.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/class.tx_mailform_xajaxHandler.php']);
}