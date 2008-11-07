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
require_once(t3lib_extMgm::extPath('mailform').'tt_content_tx_mailform_config/singletons/class.tx_mailform_configData.php');
require_once(t3lib_extMgm::extPath('mailform').'tt_content_tx_mailform_config/singletons/class.tx_mailform_saveState.php');
require_once(t3lib_extMgm::extPath('mailform').'tt_content_tx_mailform_config/singletons/class.tx_mailform_tablefieldHandler.php');
require_once(t3lib_extMgm::extPath('mailform').'tt_content_tx_mailform_config/singletons/class.tx_mailform_formHandler.php');
require_once(t3lib_extMgm::extPath('mailform')."lib/addonInterface/class.tx_mailform_extLoader.php");

/**
 * Abstract Handler class
 * For use: Use FE_Handler or BE_Handler
 *
 */
abstract class tx_mailform_Handler {

	// TS Configuration
	protected $conf;

	// Singleton Objects
	protected $saveState;
	protected $formHandler;
	protected $configData;
	protected $tablefieldHandler;
	protected $urlHandler;
	
	protected $addons; // Contains an Array With Addon Objects
	protected $extLoader; // Contains the Extension Loader. Checks all Addons
	
	protected static $instance;

	/**
	 * Constructor
	 *
	 */
	protected function __construct() {
		// construct body
		$this->extLoader = tx_mailform_extLoader::getInstance();
	}

	/**
	 * public abstract getInstance
	 * Implement in Childs
	 *
	 */
	public static abstract function getInstance($formUid=0);
	
	public abstract function registerAddon(tx_mailform_addon $addon);
	
		/**
	 * get Save State Object
	 *
	 * @return Object
	 */
	public function getSaveState() {
		if(!empty($this->saveState))
			return $this->saveState;
		else
			throw new Exception('Error: saveState is not loaded');
	}

	/**
	 * get form handler object
	 *
	 * @return Object
	 */
	public function getFormHandler() {
	  if(!empty($this->formHandler))
			return $this->formHandler;
		else
			throw new Exception('Error: Form Handler is not loaded');
	}

	/**
	 * get config data object
	 *
	 * @return Object
	 */
	public function getConfigData() {
	  if(!empty($this->configData))
			return $this->configData;
		else
		  throw new Exception('Error: Config Data is not loaded');
	}
	
	/**
	 * get Save State Object
	 *
	 * @return Object
	 */
	public function getTablefieldHandler() {
		if(!empty($this->tablefieldHandler))
			return $this->tablefieldHandler;
		else
			throw new Exception('Error: Tablefield Handler is not loaded');
	}
	
/**
	 * get Url Handler
	 *
	 * @return Object
	 */
	public function getUrlHandler() {
	  if(!empty($this->urlHandler))
			return $this->urlHandler;
		else
			throw new Exception('Error. Url Handler is not loaded');
	}
	
	/**
	 *  get TS Configuration
	 *  if not loaded use Load Functions
	 *
	 * @return Array
	 */
	public function getTSConf() {
	  if(!empty($this->conf))
			return $this->conf;
		else
			throw new Exception('TS Configuration is not loaded');
	}
	
	
	/**
	 * Resets the FE Form, page position. (whole user info)
	 *
	 */
	public function resetFormular() {
		foreach($this->fieldElements as $row) {
			foreach($row as $field) {
				$field->getForm()->resetForm(); 
			}
		}
		unset($_SESSION['tx_mailform'][$this->getMailformUID()]);
	}
}