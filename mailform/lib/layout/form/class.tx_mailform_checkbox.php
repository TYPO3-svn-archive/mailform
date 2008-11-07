<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* Attribute Factory Class. Every Entity does manage his attributes with that factory
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
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_checked.php");

class tx_mailform_checkbox extends tx_mailform_input implements tx_mailform_Iattr_checked {
	
	public function __construct() {
		parent::__construct();
		$this->setType('checkbox');
	}
	
	public function getChecked() {
		$this->attributes->getAttribute('tx_mailform_attr_checked');
	}
	
	public function setChecked($checked) {
		$this->addAttribute(new tx_mailform_attr_checked($checked));
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/layout/form/class.tx_mailform_checkbox.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/layout/form/class.tx_mailform_checkbox.php']);
}
?>