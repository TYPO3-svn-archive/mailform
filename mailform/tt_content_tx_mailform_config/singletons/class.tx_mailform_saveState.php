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
require_once(t3lib_extMgm::extPath('mailform').'lib/controller/class.tx_mailform_observer.php');
/**
 * tx_mailform_saveState
 *
 * @author Sebastian Winterhalder <sw@internetgalerie.ch>
 *
 */
class tx_mailform_saveState extends tx_mailform_observer {

	// class variables
	private $state;
	public static $conf_UID;

	private static $selfInstance;

	/**
	 * Private Constructor (Singleton Holder)
	 * Do only create this class over tx_mailform_tablefieldHandler::getInstance();
	 *
	 */
	private function __construct() {
		
	}

	/**
	 * Get Instance()
	 *
	 * @return Object
	 */
	public static function getInstance($uid=0) {

		if(!isset(tx_mailform_saveState::$selfInstance) || (tx_mailform_saveState::$conf_UID != $uid && $uid != 0)) {
			tx_mailform_saveState::$conf_UID = $uid;
			tx_mailform_saveState::$selfInstance = new tx_mailform_saveState();
		}
		return tx_mailform_saveState::$selfInstance;
	}

	/**
	 * hasChanged();
	 *
	 * @return Boolean
	 */
	public function hasChanged() {
		if(!($_SESSION['tx_mailform'][tx_mailform_saveState::$conf_UID]['config_changed'] === 0 || $_SESSION['tx_mailform'][tx_mailform_saveState::$conf_UID]['config_changed'] === 1)) {

			$sql = $GLOBALS['TYPO3_DB']->SELECTquery('tx_mailform_changed', 'tt_content', 'uid='.tx_mailform_saveState::$conf_UID);
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($GLOBALS['TYPO3_DB']->sql_query($sql));
			$_SESSION['tx_mailform'][tx_mailform_saveState::$conf_UID]['config_changed'] = $row['tx_mailform_changed'] ? 1 : 0;
		}

		$intVal = $_SESSION['tx_mailform'][tx_mailform_saveState::$conf_UID]['config_changed'] ? true:false;
		return $intVal;
	}

	/**
	 * setChanged($boolean=true)
	 *
	 * @param Boolean $boolean
	 */
	public function setChanged($boolean=true) {
		$intVal = $boolean ? 1:0;

		if($boolean == true) {
			if(!$this->hasChanged())
				$this->setChangedQuery(true);
		} else {
			$this->setChangedQuery(false);
		}
	}

	/**
	 * setChangedQuery($boolean)
	 *
	 * @param Boolean $boolean
	 */
	private function setChangedQuery($boolean) {
		$intVal = $boolean ? 1:0;

		$sql = $GLOBALS['TYPO3_DB']->UPDATEquery('tt_content', 'uid='.tx_mailform_saveState::$conf_UID, array('tx_mailform_changed' => $intVal));
		$GLOBALS['TYPO3_DB']->sql_query($sql);

		$_SESSION['tx_mailform'][tx_mailform_saveState::$conf_UID]['config_changed'] = $intVal;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/tt_content_tx_mailform_config/singletons/class.tx_mailform_saveState.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/tt_content_tx_mailform_config/singletons/class.tx_mailform_saveState.php']);
}
?>