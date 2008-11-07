<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* Interface Alt
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
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_I_layout.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/class.tx_mailform_parent.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/form/class.tx_mailform_option.php");

require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_disabled.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_multiple.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_name.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_onblur.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_onchange.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_onfocus.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_onselect.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_size.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_tableindex.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_type.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_checked.php");

/**
* Interface alt
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
class tx_mailform_input extends tx_mailform_parent implements tx_mailform_I_layout,
																				tx_mailform_Iattr_value,
																				tx_mailform_Iattr_disabled,
																				tx_mailform_Iattr_multiple,
																				tx_mailform_Iattr_name,
																				tx_mailform_Iattr_onblur,
																				tx_mailform_Iattr_onchange,
																				tx_mailform_Iattr_onfocus,
																				tx_mailform_Iattr_size,
																				tx_mailform_Iattr_tableindex,
																				tx_mailform_Iattr_type,
																				tx_mailform_Iattr_onselect,
																				tx_mailform_Iattr_checked
																				  {

	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * Gibt die generierte Tabelle zur�ck
	 *
	 * @return String
	 */
	public function getElementRendered() {
		$this->appendString("<input");
		$this->appendString($this->attributes->getAttributes());
		$this->appendString(">\n");
		return $this->getResult();
	}
	
	public function setValue($value) {
		$this->addAttribute(new tx_mailform_attr_value($value));
	}
	
	public function getValue() {
		return $this->getAttribute('tx_mailform_attr_value');
	}
	
	/* Interface Implementation */
	public function setDisabled($boolean) {
		$this->addAttribute(new tx_mailform_attr_disabled($boolean));
	}
	
	public function getDisabled() {
		return $this->getAttribute('tx_mailform_attr_disabled');
	}
	
	public function setMultiple($boolean) {
		$this->addAttribute(new tx_mailform_attr_multiple($boolean));
	}
	
	public function getMultiple() {
		return $this->getAttribute('tx_mailform_attr_multiple');
	}
	
	public function setName($name) {
		$this->addAttribute(new tx_mailform_attr_name($name));
	}
	
	public function getName() {
		return $this->getAttribute('tx_mailform_attr_name');
	}
	
	public function setOnblur($onblur) {
		$this->addAttribute(new tx_mailform_attr_onblur($onblur));
	}
	
	public function getOnblur() {
		return $this->getAttribute('tx_mailform_attr_onblur');
	}
	
	public function setOnchange($onchange) {
		$this->addAttribute(new tx_mailform_attr_onchange($onchange));
	}
	
	public function getOnchange() {
		return $this->getAttribute('tx_mailform_attr_onchange');
	}
	
	public function setOnfocus($onfocus) {
		$this->addAttribute(new tx_mailform_attr_onfocus($onfocus));
	}
	
	public function getOnfocus() {
		return $this->getAttribute('tx_mailform_attr_onfocus');
	}
	
	public function setSize($size) {
		$this->addAttribute(new tx_mailform_attr_size($size));
	}
	
	public function getSize() {
		return $this->getAttribute('tx_mailform_attr_size');
	}
	
	public function setTableindex($tableindex) {
		$this->addAttribute(new tx_mailform_attr_tableindex($tableindex));
	}
	
	public function getTableindex() {
		return $this->getAttribute('tx_mailform_attr_tableindex');
	}
	
	public function setType($type) {
		if(
		$type == tx_mailform_attr_type::ATTR_TYPE_BUTTON ||
		$type == tx_mailform_attr_type::ATTR_TYPE_CHECKBOX ||
		$type == tx_mailform_attr_type::ATTR_TYPE_FILE  ||
		$type == tx_mailform_attr_type::ATTR_TYPE_HIDDEN  ||
		$type == tx_mailform_attr_type::ATTR_TYPE_IMAGE  ||
		$type == tx_mailform_attr_type::ATTR_TYPE_PW  ||
		$type == tx_mailform_attr_type::ATTR_TYPE_RADIO  ||
		$type == tx_mailform_attr_type::ATTR_TYPE_RESET  ||
		$type == tx_mailform_attr_type::ATTR_TYPE_SUBMIT  ||
		$type == tx_mailform_attr_type::ATTR_TYPE_TEXT 
		){
			$this->addAttribute(new tx_mailform_attr_type($type));
		} else
			throw new Exception('Not allowed Input-Type given');
	}
	
	public function getType() {
		return $this->getAttribute('tx_mailform_attr_type');
	}
	
	public function setOnselect($select) {
		$this->addAttribute(new tx_mailform_attr_onselect($select));
	}
	
	public function getOnselect() {
		return $this->getAttribute('tx_mailform_attr_onselect');
	}

	public function setChecked($value) {
		$this->addAttribute(new tx_mailform_attr_checked($value));
	}
	
	public function getChecked() {
		return $this->getAttribute('tx_mailform_attr_checked');
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/layout/form/class.tx_mailform_input.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/layout/form/class.tx_mailform_input.php']);
}
?>