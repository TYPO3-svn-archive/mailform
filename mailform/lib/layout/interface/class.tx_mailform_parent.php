<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* Class Parent
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
require_once(t3lib_extMgm::extPath('mailform').'lib/layout/attributes/class.tx_mailform_attributeFactory.php');

require_once(t3lib_extMgm::extPath('mailform').'lib/layout/interface/interface.tx_mailform_Iattr_style.php');
require_once(t3lib_extMgm::extPath('mailform').'lib/layout/interface/interface.tx_mailform_Iattr_cssclass.php');
require_once(t3lib_extMgm::extPath('mailform').'lib/layout/interface/interface.tx_mailform_Iattr_id.php');
require_once(t3lib_extMgm::extPath('mailform').'lib/layout/interface/interface.tx_mailform_Iattr_lang.php');
require_once(t3lib_extMgm::extPath('mailform').'lib/layout/interface/interface.tx_mailform_Iattr_title.php');
require_once(t3lib_extMgm::extPath('mailform').'lib/layout/interface/interface.tx_mailform_Iattr_dir.php');

require_once(t3lib_extMgm::extPath('mailform').'lib/layout/attributes/class.tx_mailform_attr_style.php');
require_once(t3lib_extMgm::extPath('mailform').'lib/layout/attributes/class.tx_mailform_attr_cssclass.php');
require_once(t3lib_extMgm::extPath('mailform').'lib/layout/attributes/class.tx_mailform_attr_id.php');
require_once(t3lib_extMgm::extPath('mailform').'lib/layout/attributes/class.tx_mailform_attr_lang.php');
require_once(t3lib_extMgm::extPath('mailform').'lib/layout/attributes/class.tx_mailform_attr_title.php');
require_once(t3lib_extMgm::extPath('mailform').'lib/layout/attributes/class.tx_mailform_attr_dir.php');

/**
* Class Parent
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
abstract class tx_mailform_parent implements tx_mailform_I_layout,
													tx_mailform_Iattr_style,
													tx_mailform_Iattr_cssclass,
													tx_mailform_Iattr_id,
													tx_mailform_Iattr_lang,
													tx_mailform_Iattr_title,
													tx_mailform_Iattr_dir
													
		{

	protected $attributes;
	private $result = "";
	protected $comment;
	
	public function __construct() {
		$this->attributes = new tx_mailform_attributeFactory();
	}
	
	/**
	 * Wrapper
	 *
	 * @param Object $attribute
	 */
	protected function addAttribute($attribute) {
		$this->attributes->addAttribute($attribute);
	}
	
	/**
	 * Get Attribute
	 *
	 * @param String $name
	 * @return Object
	 */
	protected function getAttribute($name) {
		return $this->attributes->getAttribute($name);
	}
		
	protected function appendString($string) {
		$this->result .= $string;
	}
	
	protected function getResult() {
		return $this->result;
	}
	
	public function setComment($comment) {
		$this->comment = $comment;
	}
	
	public function resetString() {
		$this->result = '';
	}
	
	/** 
	 * 
	 * Interface 
	 * 
	 * */
	
	/**
	 * F�gt eine CSS Klasse hinzu
	 *
	 * @param String $class
	 */
	public function addCssClass($class) {
		if(!($attr = $this->getAttribute('tx_mailform_attr_cssclass')))
			$this->attributes->addAttribute(new tx_mailform_attr_cssclass($class));
		else {
			$attr->addCssClass($class);
			$this->attributes->addAttribute($attr);
		}
	}
	
	/**
	 * Get all classes in an array
	 *
	 * @return Array
	 */
	public function getCssClasses() {
		return $this->getAttribute('tx_mailform_attr_cssclass');
	}
	
	/**
	 * F�gt ein Style Element hinzu
	 *
	 * @param String $style
	 */
	public function addStyle($style) {
		if(!($attr = $this->getAttribute('tx_mailform_attr_style')))
			$this->attributes->addAttribute(new tx_mailform_attr_style($style));
		else {
			$attr->addStyle($style);
			$this->attributes->addAttribute($attr);
		}
	}
	
	/**
	 * Gibt alle Styles zur�ck
	 *
	 * @return Mixed
	 */
	public function getStyles() {
		return $this->getAttribute('tx_mailform_attr_style');
	}
	
	public function setDir($dir) {
		$this->addAttribute(new tx_mailform_attr_dir($dir));
	}
	
	public function getDir() {
		return $this->getAttribute('tx_mailform_attr_dir');
	}
	
	public function setId($id) {
		$this->addAttribute(new tx_mailform_attr_id($id));
	}
	
	public function getId() {
		return $this->getAttribute('tx_mailform_attr_id');
	}
	
	public function setLang($lang) {
		$this->addAttribute(new tx_mailform_attr_lang($lang));
	}
	
	public function getLang() {
		return $this->getAttribute('tx_mailform_attr_lang');
	}
	
	public function setTitle($title) {
		$this->addAttribute(new tx_mailform_attr_title($title));
	}
	
	public function getTitle() {
		$this->getAttribute('tx_mailform_attr_title');
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/layout/interface/class.tx_mailform_parent.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/layout/interface/class.tx_mailform_parent.php']);
}
?>