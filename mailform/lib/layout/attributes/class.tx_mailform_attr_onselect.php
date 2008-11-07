<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* Class Name
*
*
* PHP versions 4 and 5
*
* Copyright notice
*
* (c) 2007 Sebastian Winterhalder <sebi@concastic.ch>
* All rights reserved
*
* This script is part of the TYPO3 project. The TYPO3 project is
* free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
*
*/

/*
* Place includes, constant defines and $_GLOBAL settings here.
*/
require_once(t3lib_extMgm::extPath('mailform').'lib/layout/attributes/class.tx_mailform_attribute.php');
require_once(t3lib_extMgm::extPath('mailform').'lib/layout/interface/interface.tx_mailform_Iattr_onselect.php');

/**
* Class Name
*
* 
*
* @category   HTML Library
* @package    interface
* @author     Sebastian Winterhalder <sebi@concastic.ch>
* @copyright  2007 Concastic
* @license    http://www.gnu.org/copyleft/gpl.html.
* @version    Release: @package_version@
* @since      Class available since Release 1.0.0
*/
class tx_mailform_attr_onselect extends tx_mailform_attribute implements tx_mailform_Iattr_onselect {
	
	public function __construct($onselect=-1) {
		$this->setOnselect($onselect);
	}
	
	/**
	 * Gibt das Attribut zur�ck
	 *
	 * @return String
	 */
	public function getAttribute() {
		if($this->getOnselect() != -1)
			return ' onselect="'.$this->attribute.'"';
		else
			return '';
	}
	
	/**
	 * Setze onselect
	 * Keine Ausgabe -> $label = -1
	 * 
	 * @param String $label
	 */
	public function setOnselect($onselect) {
		$this->setAttribute($onselect);
	}
	
	/**
	 * Gibt onselect zur�ck
	 * Falls kein onselect angegeben ist die Ausgabe -1
	 * 
	 * @return String
	 */
	public function getOnselect() {
		return $this->attribute;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/layout/attributes/class.tx_mailform_attr_onselect.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/layout/attributes/class.tx_mailform_attr_onselect.php']);
}
?>