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
* mailform module tx_mailform_templateObject
*
* @author       Sebastian Winterhalder <sw@internetgalerie.ch>
* 
*/
class  tx_mailform_templateObject {
	
	private $html;
	private $elementType;
	private $formType;
	private $specialType; // If is a sub template of a element type it has a value
	private $outputKey;
	private $name;
	private $subparts = array();
  
	/**
	* Constructor
	*
	*/
	public function __construct() { 
		$this->html = "";
		$this->type = "";
		$this->outputKey = array();
		$this->name = "";
		$this->specialType = tx_mailform_templateParser::$unallowedArrayKey; // Standard Key
	}
  
	/**
	* Main
	*
	*/
	public function main() {
		$this->__construct();
	}
	
	public function getElementType() {
		return $this->elementType;
	}
  
	public function getFormType() {
		return $this->formType;
	}
	
	public function setSpecialType($type) {
		$this->specialType = $type;
	}
	
	public function getSpecialType() {
		return $this->specialType;
	}
  
	public function appendHtml($html) {
		$this->html .= $html;
	}
  
	public function setElementType($type) {
		$this->elementType = $type;
	}
  
	public function setFormType($type) {
		$this->formType = $type;
	}
  
	public function addOutput($key, $output) {
		$this->outputKey[$key] = $output;
	}
	
	public function addSubpartMarkerArray($markerArray) {
		foreach($markerArray as $key => $value) {
			$this->html = str_replace($key, $value, $this->html);
		}
	}
  
	public function setSubElement($templateObj) {
		if(!get_class($templateObj) == 'tx_mailform_templateObject')
			throw new Exception("Wrong argument passed. Only tx_mailform_templateObject allowed.");
		$this->subparts[] = $templateObj;
	}
	
	public function getSubElement($type, $element) {
		foreach($this->subparts as $subElement) {
			if($subElement->getFormType() == $type && $subElement->getElementType() == $element) {
				return $subElement;
			}
		}
		throw new Exception("The Sub Element Template: '".$type."' Element:'".$element."' could not be found");
	}
  
	public function getParsedHtml() {
		$result = $this->html;
		foreach($this->outputKey as $key => $value) {
			$replacePattern = '###OUTPUT_'.strtoupper($key)."###";
			$result = str_replace($replacePattern, $value, $result);
		}
		return $result;
	}
	
	public function __toString() {
		return "templateObject: ".$this->getFormType()." - ".$this->getElementType();
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/templateParser/class.tx_mailform_templateObject.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/templateParser/class.tx_mailform_templateObject.php']);
}
?>