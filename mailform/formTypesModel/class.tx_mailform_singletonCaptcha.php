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
 * tx_mailform_singletonCaptcha
 *
 * @to add an additional form type, register the form type in tx_mailform_form.
 * @author       Sebastian Winterhalder <sw@internetgalerie.ch>
 * 
 */
class tx_mailform_singletonCaptcha {
	
	private static $instance;
	private $freeCap;
	private $valid = false;
	
	/**
	 * Constructor
	 *
	 * @param String $word
	 */
	private function __construct($word) {
	// code inserted to use free Captcha
		if (t3lib_extMgm::isLoaded('sr_freecap') ) {
			require_once(t3lib_extMgm::extPath('sr_freecap').'pi2/class.tx_srfreecap_pi2.php');
			$this->freeCap = t3lib_div::makeInstance('tx_srfreecap_pi2');
		} else {
			die('<b>Error:</b> The extension sr_freecap is not loaded');
		}
		
		if($this->freeCap->checkWord($word))
			$this->valid = true;
	}

	/**
	 * getInstance($word=false)
	 *
	 * @param String $word
	 * @return Object
	 */
	public static function getInstance($word=false) {
		if(empty(self::$instance))
			self::$instance = new tx_mailform_singletonCaptcha($word);
		return self::$instance;
	}

	/**
	 * isValid()
	 *
	 * @return Boolean
	 */
	public function isValid() {
		return $this->valid;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/class.tx_mailform_singletonCaptcha.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/class.tx_mailform_singletonCaptcha.php']);
}