<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* Interface Alt
*
*
* PHP versions 5
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
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_I_multipleContent.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/class.tx_mailform_parent.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/form/class.tx_mailform_option.php");

require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_disabled.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_multiple.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_name.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_onblur.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_onchange.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_onfocus.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_size.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_tableindex.php");

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
class tx_mailform_select extends tx_mailform_parent implements tx_mailform_I_layout,
																				tx_mailform_I_multipleContent,
																				tx_mailform_Iattr_disabled,
																				tx_mailform_Iattr_multiple,
																				tx_mailform_Iattr_name,
																				tx_mailform_Iattr_onblur,
																				tx_mailform_Iattr_onchange,
																				tx_mailform_Iattr_onfocus,
																				tx_mailform_Iattr_size,
																				tx_mailform_Iattr_tableindex  {

	protected $option_elements = array();

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Gibt die generierte Tabelle zur�ck
	 *
	 * @return String
	 */
	public function getElementRendered() {
		$this->appendString("<select");
		$this->appendString($this->attributes->getAttributes());
		$this->appendString(">\n");
		foreach($this->option_elements as $key => $element) {
			$this->appendString($element->getElementRendered());
		}
		$this->appendString("</select>\n");
		return $this->getResult();
	}

	public function addContent($arg) {
		if(!$arg instanceof tx_mailform_option)
			throw new Exception('The given Argument does not match the type. tx_mailform_option expected');
		$this->option_elements[] = $arg;
	}

	public function getContent() {
		return implode("\n", $this->option_elements);
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

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/layout/form/class.tx_mailform_select.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/layout/form/class.tx_mailform_select.php']);
}
?>