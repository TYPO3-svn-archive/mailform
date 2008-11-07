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
/**
* mailform module tx_mailform_sendOperator
*
* @to add an additional form type, register the form type in tx_mailform_form.
*
* @author       Sebastian Winterhalder <sw@internetgalerie.ch>
*/
class tx_mailform_sendOperator {
	
	private static $instance;
	private $sendable;
	private $errors;
	
	/**
	 * Singleton Holder Instance Creator
	 *
	 * @return Object
	 */
	public static function getInstance() {
		if(empty(self::$instance))
			self::$instance = new tx_mailform_sendOperator();
		return self::$instance;
	}
	
	/**
	 * Private Class Constructor
	 *
	 */
	private function __construct() {
		$this->setSendable(true);
	}
	
	/**
	 * Gives boolean value Back, true if configuration has no errors
	 *
	 * @return boolean
	 */
	public function isSendable() {
		if(!is_bool($this->sendable))
			throw new Exception('Boolean expected. Please initialize this class properly');
		return $this->sendable;
	}
	
	/**
	 * Adds an error to the protocol. If errors ocurr the emails will not be sent
	 *
	 * @param Array $sendError
	 */
	public function addError($sendError) {
		$this->setSendable(false);
		$this->errors[] = $sendError;
	}
	
	/**
	 * Returns all Errors in an array
	 *
	 * @return Array
	 */
	public function getErrors() {
		return $this->errors;
	}
	
	/**
	 * Private: set Sendable to boolean value
	 *
	 * @param Boolean $bool
	 */
	private function setSendable($bool) {
		$this->sendable = $bool;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/pi1/mail/class.tx_mailform_sendOperator.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/pi1/mail/class.tx_mailform_sendOperator.php']);
}