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
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_I_layout.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_width.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_height.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_rowspan.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_colspan.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_align.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_valign.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/class.tx_mailform_parent.php");

/**
 * tx_mailform_table
 * 
 * Unterst�tzt das vollst�ndige erstellen einer Tabelle in OOP
 *
 * @version 1.0
 * @author Sebastian Winterhalder (sebi@concastic.ch)
 * 
 */
class tx_mailform_td extends tx_mailform_parent implements tx_mailform_I_layout,
										 	tx_mailform_Iattr_height,
										 	tx_mailform_Iattr_width,
										 	tx_mailform_Iattr_colspan,
										 	tx_mailform_Iattr_rowspan,
										 	tx_mailform_Iattr_align,
										 	tx_mailform_Iattr_valign
{
	
	/**
	 * Setze die H�he Integer oder Prozent (String)
	 *
	 * @param Mixed $height
	 */
	public function setHeight($height) {
		$this->addAttribute(new tx_mailform_attr_height($height));
	}
	
	/**
	 * Gibt die Höhe zurück
	 *
	 * @param Mixed $height
	 * @return int
	 */
	public function getHeight() {
		return $this->getAttribute('tx_mailform_attr_height');
	}
	
	/**
	 * Setze die Breite Integer oder Prozent (String)
	 *
	 * @param Mixed $width
	 */
	public function setWidth($width) {
		$this->addAttribute(new tx_mailform_attr_width($width));
	}
	
	/**
	 * Gibt die aktuelle Breite zur�ck
	 *
	 * @return Mixed
	 */
	public function getWidth() {
		return $this->getAttribute('tx_mailform_attr_width');
	}
	
	/**
	 * Setze Align
	 *
	 * @param String $align
	 */
	public function setAlign($align) {
		$this->addAttribute(new tx_mailform_attr_align($align));
	}
	
	/**
	 * Gibt align zurück
	 *
	 * @return Object
	 */
	public function getAlign() {
		return $this->getAttribute('tx_mailform_attr_align');
	}
		
	/**
	 * Setze Align
	 *
	 * @param String $align
	 */
	public function setValign($align) {
		$this->addAttribute(new tx_mailform_attr_valign($align));
	}
	
	/**
	 * Gibt align zurück
	 *
	 * @return Object
	 */
	public function getValign() {
		return $this->getAttribute('tx_mailform_attr_valign');
	}
	
	/**
	 * Fügt eine CSS Klasse hinzu
	 *
	 * @param String $class
	 */
	public function addCssClass($class) {
		if(!($attr = $this->attributes->getAttribute('tx_mailform_attr_cssclass')))
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
		return $this->attributes->getAttribute('tx_mailform_attr_cssclass');
	}
	
	/**
	 * F�gt ein Style Element hinzu
	 *
	 * @param String $style
	 */
	public function addStyle($style) {
		if(!($attr = $this->attributes->getAttribute('tx_mailform_attr_style')))
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
		return $this->attributes->getAttribute('tx_mailform_attr_style');
	}
	
	/**
	 * Setze rowspan
	 *
	 * @param unknown_type $rowspan
	 */
	public function setRowspan($rowspan) {
	  if(!$rowspan > 0)
	    $rowspan = 1;
	    
		$this->addAttribute(new tx_mailform_attr_rowspan($rowspan));
	}
	
	public function getRowspan() {
		$this->getAttribute('tx_mailform_attr_rowspan');
	}
	
	/**
	 * Setze colspan
	 *
	 * @param int $colspan
	 */
	public function setColspan($colspan) {
	  if(!$colspan > 0)
	    $colspan = 1;
	    
		$this->addAttribute(new tx_mailform_attr_colspan($colspan));
	}
	
	public function getColspan() {
		$this->getAttribute('tx_mailform_attr_colspan');
	}
	
	/**
	 * Gibt das Generierte HTML Element aus
	 *
	 */
	public function getElementRendered() {
		if(!empty($this->comment))
			$res = "\n<!-- Class TD: ".$this->comment." -->\n";
		$res .= "\n<td";
		$res .= $this->attributes->getAttributes();
		$res .= ">".$this->content."</td>\n";
		if(!empty($this->comment))
			$res .= "\n<!-- END Class TD: ".$this->comment." -->\n";
		return $res;
	}
	
	public function setContent($content) {
		$this->content = $content;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/layout/table/class.tx_mailform_td.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/layout/table/class.tx_mailform_td.php']);
}
?>
