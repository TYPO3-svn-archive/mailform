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
//require_once(t3lib_extMgm::extPath('mailform').'hooks/class.tx_mailform_FE_Handler.php');

/**
 * tx_mailform_parseVariable
 *
 * @author Sebastian Winterhalder <sw@internetgalerie.ch>
 * 
 */   
class tx_mailform_parseVariable {

	private $ID;
	private $type;
	private $arrayKey;
	private $origString;
	private $formElement;
	private $uidExists = false;
	
	public function __construct($ID) {
		$this->setID($ID);
	}
	
	public function setID($ID) {
		$feHandler = tx_mailform_FE_Handler::getInstance();
		$fieldElements = $feHandler->getFieldElements();
		
		// Check reference if ID Exists
		foreach($fieldElements as $xRow) {
			foreach($xRow as $formObj) {
				if($formObj->getForm()->getUFID() == $ID) {
					$this->uidExists = true;
					$this->formElement = $formObj;
					break;
				}
			}
		}
		
		$this->ID = $ID;
	}
	
	public function getID() {
		return $this->ID;
	}
	
	public function setType($type) {
		$this->type = $type;
	}
	
	public function getType() {
		return $this->type;
	}
	
	public function setArrayKey($arrayKey) {
		$this->arrayKey = $arrayKey;
	}
	
	public function getArrayKey() {
		return $this->arrayKey;
	}
	
	public function setOriginalString($string) {
		$this->origString = $string;
	}
	
	public function getOriginalString() {
		return $this->origString;
	}
	
	public function getValue() {
		if($this->isVar()) {
			if(!empty($this->arrayKey))	{
				$str = $this->formElement->getForm()->getConfigData();
				return $str[$this->arrayKey];
			} else {
				$var = $this->formElement->getForm()->getFieldValue();
				if(is_array($var)) {
					$var2 = $this->formElement->getForm()->getPostValue();
					if(is_array($var2)) {
						$res = '';
						foreach($var2 as $element) {
							if($element['checked'] == 1) {
								if(strlen($res) > 0)
									$res .= ",";
								$res .= $element['value'];
							}
						}
					}
					return $res;
				} else {
					return $var;
				}
			}
		} else {
			return $this->getOriginalString();
		}
	}
	
	private function isVar() {
		return ($this->getType() != "" && $this->getID() != "" && $this->uidExists);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/templateParser/class.tx_mailform_parseVariable.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/templateParser/class.tx_mailform_parseVariable.php']);
}
?>