<?php
session_start();
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
require_once(t3lib_extMgm::extPath('mailform').'lib/controller/class.tx_mailform_observer.php');
require_once(t3lib_extMgm::extPath('mailform').'tt_content_tx_mailform_config/singletons/class.tx_mailform_saveState.php');
require_once(t3lib_extMgm::extPath('mailform').'lib/database/class.tx_mailform_db_ttContentRow.php');

/**
 * tx_mailform_configData
 * 
 * @author Sebastian Winterhalder <sw@internetgalerie.ch>
 *
 */
class tx_mailform_configData extends tx_mailform_observer {
	
	// class variables
	private $totalConf;
	private $flexform; // Only FE Mode
	private $pageConf;
	public static $conf_UID = false;
	
	private static $selfInstance;
	
	/**
	 * Private Constructor (Singleton Holder)
	 * Do only create this class over tx_mailform_tablefieldHandler::getInstance();
	 *
	 */
	private function __construct($uid=false) {
		// Initialize variables
		$this->loadTotalConfig($uid);
	}

	/**
	 * loadTotalConfig()
	 *
	 */
	private function loadTotalConfig($uid=false) {
		if($uid != false && $uid > 0)
			tx_mailform_configData::$conf_UID = $uid;
		
		if(TYPO3_MODE == "BE"){
			$this->loadBE($uid);
		}
		elseif(TYPO3_MODE == "FE")
			$this->loadFE($uid);
		else
			throw new Exception("This Typo3 Mode is not allowed: ".TYPO3_MODE);
			
	}
	
	private function loadFE($uid=false) {
		if(TYPO3_MODE != "FE")
			throw new Exception("This function loadFE is only allowed in Frontend");
		require_once(t3lib_extMgm::extPath('mailform')."lib/database/class.tx_mailform_db_ttContentRow.php");
		
		$contentRow = tx_mailform_db_ttContentRow::getInstance($uid);

		$this->totalConf = $contentRow->getConfigArray();
		$this->flexform = $contentRow->getFlexformArray();
		
		$this->checkArrayNotNull();
	}
	
	private function loadBE($uid=false) {
		if(TYPO3_MODE != "BE")
			t3lib_BEfunc::typo3PrintError ('This is a BE Function', 'Frontend call not allowed. Contact developer.', 0);

		$contentRow = tx_mailform_db_ttContentRow::getInstance($uid);
		$this->totalConf = $contentRow->getConfigArray();

		$this->flexform = $contentRow->getFlexformArray();

		$this->checkArrayNotNull();

		$ss = tx_mailform_saveState::getInstance(tx_mailform_configData::$conf_UID);
		if($ss->hasChanged()) {
			if(isset($_SESSION['tx_mailform'][tx_mailform_configData::$conf_UID]['mailform_config']) && isset($_SESSION['tx_mailform'][tx_mailform_configData::$conf_UID]['mailform_forms'])) {
				$this->totalConf = $_SESSION['tx_mailform'][tx_mailform_configData::$conf_UID];
			} else {
				$this->loadBE_Sub();
			}
		} else {
			$this->loadBE_Sub();
		}

		$this->makeConfigDataPersistent();
	}
	
	/**
	 * Sub Function from loadBE()
	 *
	*/
	private function loadBE_Sub() {
		if(TYPO3_MODE != "BE")
			t3lib_BEfunc::typo3PrintError ('This is a BE Function', 'Frontend call not allowed. Contact developer.', 0);

		$row = t3lib_BEfunc::getRecord('tt_content', tx_mailform_configData::$conf_UID);
		if(!is_array($row)) {
			t3lib_BEfunc::typo3PrintError ('Wizard Error', 'No reference to record', 0);
			exit;
		}
		// This will get the content of the form configuration code field to us - possibly cleaned up, saved to database etc. if the form has been submitted in the meantime.
     	$this->totalConf = $this->getConfigCode($row);

		$this->checkArrayNotNull();
	}
	
	private function checkArrayNotNull() {
		if(!is_array($this->totalConf['mailform_config']))
			$this->totalConf['mailform_config'] = array(array());
		if(!is_array($this->totalConf['mailform_forms']))
			$this->totalConf['mailform_forms'] = array(array());
		if(!is_array($this->totalConf['mailform_pageconf']))
			$this->totalConf['mailform_pageconf'] = array(array());
	}
	
	/**
	 * Will get and return the configuration code string
	 *
	 * @param	array		Current parent record row (passed by value!)
	 * @return	array		Configuration Array
	 * @access private
	 */
	private function getConfigCode($row) {
		$cfgArr = t3lib_div::xml2array($row['tx_mailform_config']);

		$cfgArr = is_array($cfgArr) ? $cfgArr : array();
		if (!$cfgArr) $cfgArr[][] = array('type' => '');
		return $cfgArr;
	}
	
	/**
	 * getConfigData()
	 *
	 * @return Array
	 */
	public function getConfigData() {
		return $this->totalConf['mailform_forms'];
	}
	
	/**
	 * getTotal Conf
	 *
	 * @return unknown
	 */
	public function getTotalConf() {
		return $this->totalConf;
	}
	
	public function getFlexform() {
		if(TYPO3_MODE != "FE")
			throw new Exception("Flexform is only allowed in FE Mode");
		return $this->flexform;
	}
	
	/**
	 * getFieldData()
	 *
	 * @return Array
	 */
	public function getFieldData() {
		if(is_array($this->totalConf['mailform_config'][0]))
			$arrKey = array_keys($this->totalConf['mailform_config'][0]);
		else
			$arrKey = array();
		
		if($this->totalConf['mailform_config'][0][$arrKey[0]] == "" && sizeof($this->totalConf['mailform_config']) <= 1) {
			$arr = array('field_config_colspan' => 1,
                            'field_config_rowspan' => 1,
                            'field_config_colIndex' => 0,
                            'field_config_rowIndex' => 0,
                            'field_config_cssclass' => 'td-no-style',
                            'field_config_width' => 0,
                            'field_config_height' => 0 ,
							'field_config_condition' => '',
                            'field_config_page' => 0,
                            'field_config_placeholder_index' => false ,
                            'field_config_form_keys' => '');
			return array($arr);	
		}
		return $this->totalConf['mailform_config'];
	}
	
	public function getPageConfig() {
		if(is_array($this->totalConf['mailform_pageconf'][0]))
			$arrKey = array_keys($this->totalConf['mailform_pageconf'][0]);
		else
			$arrKey = array();
		
		if($this->totalConf['mailform_pageconf'][0][$arrKey[0]] == "" && sizeof($this->totalConf['mailform_pageconf']) <= 1) {
			$arr = array('singlevalidation' => 1,
                            'pagetitle' => "");
			return array($arr);	
		}
		return $this->totalConf['mailform_pageconf'];
	}

	/**
	 * getCompleteXML();
	 *
	 * @return String
	 */
	public function getCompleteXML() {
		$rawArray['mailform_forms'] = $this->getConfigData();
		$rawArray['mailform_config'] = $this->getFieldData();
		$rawArray['mailform_pageconf'] = $this->getPageConfig();

		return t3lib_div::array2xml_cs($rawArray, 'mailform');
	}
	
	/**
	 * setConfigData($array)
	 *
	 * @param Array $array
	 */
	public function setConfigData($array) {
		$this->totalConf['mailform_forms'] = $array;
		$this->makeConfigDataPersistent();
	}
	
	/**
	 * setFieldData($array)
	 * 
	 * @param Array $array
	 */
	public function setFieldData($array) {
		$this->totalConf['mailform_config'] = $array;
		$this->makeConfigDataPersistent();
	}
	
	public function setPageconfData($array) {
		//throw new Exception();
		$this->totalConf['mailform_pageconf'] = $array;
		$this->makeConfigDataPersistent();
	}
	
	/**
	 * getFields()
	 *
	 */
	public function getFields() {
		throw new Exception('This Function is DEPRECATED -> getFields() in class.tx_mailform_configData');
	}

	/**
	 * getFormUID()
	 *
	 * @return unknown
	 */
	public function getFormUID() {
		return tx_mailform_configData::$conf_UID;
	}
	
	/**
	 * makeConfigDataPersistent()
	 *
	 */
	public function makeConfigDataPersistent() {
		$_SESSION['tx_mailform'][tx_mailform_configData::$conf_UID] = $this->totalConf;
	}
	
	/**
	 * getInstance()
	 *
	 * @return Object
	 */
	public static function getInstance($uid=false) {
		if(!isset(tx_mailform_configData::$selfInstance) || ($uid != tx_mailform_configData::$conf_UID && ($uid != false))) {
			tx_mailform_configData::$selfInstance = new tx_mailform_configData($uid);
			tx_mailform_configData::$conf_UID = $uid;
		}
		
		return tx_mailform_configData::$selfInstance;
	}
	
	/**
	 * This Function is used only, when the Document is saved
	 * index.php
	 *
	 */
	public function unsetConfigInSession() {
		unset($_SESSION['tx_mailform'][tx_mailform_configData::$conf_UID]);
		$this->loadTotalConfig();
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/tt_content_tx_mailform_config/singletons/class.tx_mailform_configData.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/tt_content_tx_mailform_config/singletons/class.tx_mailform_configData.php']);
}
?>
