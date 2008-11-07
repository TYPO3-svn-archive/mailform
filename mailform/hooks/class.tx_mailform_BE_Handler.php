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
require_once(t3lib_extMgm::extPath('mailform').'hooks/class.tx_mailform_Handler.php');

/**
 * Provides functions which can be used for hooks and addons
 * @version 0.8.5
 */
class tx_mailform_BE_Handler extends tx_mailform_Handler {

  	// Internal, static: GPvars
	public $P;
	// Wizard parameters, coming from TCEforms linking to the wizard.
	public $FORMCFG;
	// Handler for Extension Addons
	protected $wizardWrapper;
	
	public static $uid;

	/**
	 * get an instance of this class
	 *
	 * @return Object
	 */
	public static function getInstance($uid=0) {
		if(empty(self::$instance) || ($uid != 0 && $uid != tx_mailform_BE_Handler::$uid)) {
			tx_mailform_BE_Handler::$uid = $uid;
			self::$instance = new tx_mailform_BE_Handler(tx_mailform_BE_Handler::$uid);
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 *
	 */
	protected function __construct() {
	  global $LANG;
		if(TYPO3_MODE != 'BE')
			throw new Exception('This class BE_Handler can only be used if used in Frontend Plugins');
		parent::__construct();

		$this->includeLLFile('EXT:mailform/tt_content_tx_mailform_config/locallang.xml');
		
    	$this->P = t3lib_div::_GP('P');
    	
		$this->FORMCFG = t3lib_div::_GP('FORMCFG');
		$this->page = $this->FORMCFG['actualPage'];
		$this->loadTS(tx_mailform_BE_Handler::$uid);
		
		$this->configData = tx_mailform_configData::getInstance(tx_mailform_BE_Handler::$uid);

		$this->formHandler = tx_mailform_formHandler::getInstance();
		$this->saveState = tx_mailform_saveState::getInstance();
		$this->tablefieldHandler = tx_mailform_tablefieldHandler::getInstance();
		
		$this->urlHandler = new tx_mailform_urlHandler();
	}

	public function registerAddon(tx_mailform_addon $addon) {
		
	}
	
	/**
	 *  Wrapper for IncludeLLFile()
	 *
	 */
	public function includeLLFile($string) {
		$this->getLang()->includeLLFile($string);
	}

	/**
	 * Load the TS Constants / Setup from T3 Page Template
	 *  TS Constants used for searching mailform addons
	 *
	 *
	 */
	private function loadTS($pageUid) {
    	require_once (PATH_t3lib.'class.t3lib_page.php');
    	require_once (PATH_t3lib.'class.t3lib_tstemplate.php');
    	require_once (PATH_t3lib.'class.t3lib_tsparser_ext.php');

		$sysPageObj = t3lib_div::makeInstance('t3lib_pageSelect');
		$rootLine = $sysPageObj->getRootLine($pageUid);
		$TSObj = t3lib_div::makeInstance('t3lib_tsparser_ext');
		$TSObj->tt_track = 0;
		$TSObj->init();
		$TSObj->runThroughTemplates($rootLine);
		$TSObj->generateConfig();
		
		$this->conf = $TSObj->setup['plugin.']['tx_mailform_pi1.'];
	}
	
	/**
	 *  get the Wizard Wrapper
	 *
	 *  @return Object
	 */
	public function getWizardWrapper() {
	  if(empty($this->wizardWrapper))
			$this->wizardWrapper = tx_mailform_WizardWrapper::getInstance();
			
	  return $this->wizardWrapper;
	}
	
	/**
	 * get the Language Object
	 *
	 * @return Object
	 */
	public function getLang() {
		global $LANG;
		if(empty($LANG)) {
      		$LANG = t3lib_div::makeInstance('language');
		}
		return $LANG;
	}

	/**
	 * To String
	 *
	 * @return String
	 */
	public function __toString() {
		return "class.tx_mailform_BE_Handler";
	}
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/hooks/class.tx_mailform_BE_Handler.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/hooks/class.tx_mailform_BE_Handler.php']);
}