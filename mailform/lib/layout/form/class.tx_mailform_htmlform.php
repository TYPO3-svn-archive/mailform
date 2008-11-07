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
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_I_content.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/class.tx_mailform_parent.php");

require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_action.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_accept.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_enctype.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_acceptcharset.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_method.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_name.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_onreset.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_onsubmit.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_target.php");

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
class tx_mailform_htmlform extends tx_mailform_parent implements tx_mailform_I_layout,
																				tx_mailform_Iattr_action,
																				tx_mailform_Iattr_accept,
																				tx_mailform_Iattr_enctype,
																				tx_mailform_Iattr_acceptcharset,
																				tx_mailform_Iattr_method,
																				tx_mailform_Iattr_name,
																				tx_mailform_Iattr_onreset,
																				tx_mailform_Iattr_onsubmit,
																				tx_mailform_Iattr_target,
																				tx_mailform_I_content
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
		$this->resetString();
		$this->appendString("<form");
		$this->appendString($this->attributes->getAttributes());
		$this->appendString(">\n");
		$this->appendString($this->getContent());
		$this->appendString('</form>');
		return $this->getResult();
	}
	
	public function getStartElement() {
		$this->resetString();
		$this->appendString("<form");
		$this->appendString($this->attributes->getAttributes());
		$this->appendString(">");
		return $this->getResult();
	}
	
	public function getEndElement() {
		$this->resetString();
		$this->appendString('</form>');
		return $this->getResult();
	}
	

	/**
	 * Content
	 *
	 * @param Mixed $content
	 */
	public function setContent($content) {
		$this->content = $content;
	}
	
	public function getContent() {
		return $this->content;
	}
	
	public function setName($name) {
		$this->addAttribute(new tx_mailform_attr_name($name));
	}
	
	public function getName() {
		return $this->getAttribute('tx_mailform_attr_name');
	}
	
	public function setAction($value) {
		$this->addAttribute(new tx_mailform_attr_value($value));
	}
	
	public function getAction() {
		return $this->getAttribute('tx_mailform_attr_value');
	}
	
	public function setAccept($value) {
		$this->addAttribute(new tx_mailform_attr_value($value));
	}
	
	public function getAccept() {
		return $this->getAttribute('tx_mailform_attr_value');
	}
	
	public function setEnctype($value) {
		$this->addAttribute(new tx_mailform_attr_value($value));
	}
	
	public function getEnctype() {
		return $this->getAttribute('tx_mailform_attr_value');
	}
	
	public function setAcceptcharset($value) {
		$this->addAttribute(new tx_mailform_attr_value($value));
	}
	
	public function getAcceptcharset() {
		return $this->getAttribute('tx_mailform_attr_value');
	}
	
	public function setMethod($value) {
		$this->addAttribute(new tx_mailform_attr_value($value));
	}
	
	public function getMethod() {
		return $this->getAttribute('tx_mailform_attr_value');
	}
	
	public function setOnreset($value) {
		$this->addAttribute(new tx_mailform_attr_value($value));
	}
	
	public function getOnreset() {
		return $this->getAttribute('tx_mailform_attr_value');
	}
	
	public function setOnsubmit($value) {
		$this->addAttribute(new tx_mailform_attr_value($value));
	}
	
	public function getOnsubmit() {
		return $this->getAttribute('tx_mailform_attr_value');
	}
	
	public function setTarget($value) {
		$this->addAttribute(new tx_mailform_attr_value($value));
	}
	
	public function getTarget() {
		return $this->getAttribute('tx_mailform_attr_value');
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/layout/form/class.tx_mailform_htmlform.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/layout/form/class.tx_mailform_htmlform.php']);
}
?>