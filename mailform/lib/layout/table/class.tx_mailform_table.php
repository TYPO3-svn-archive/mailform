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
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/table/class.tx_mailform_tr.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_width.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_height.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_cellpadding.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_cellspacing.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_summary.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_border.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/attributes/class.tx_mailform_attr_cellpadding.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/attributes/class.tx_mailform_attr_cellspacing.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/attributes/class.tx_mailform_attr_width.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/attributes/class.tx_mailform_attr_height.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/attributes/class.tx_mailform_attr_summary.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/attributes/class.tx_mailform_attr_border.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/attributes/class.tx_mailform_attributeFactory.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/class.tx_mailform_parent.php");

/**
 * tx_mailform_table
 * 
 * Supports creating a table
 *
 * @version 1.0
 * @author Sebastian Winterhalder (sebi@concastic.ch)
 * 
 */
class tx_mailform_table extends tx_mailform_parent implements tx_mailform_I_layout,
										 	tx_mailform_Iattr_height,
										 	tx_mailform_Iattr_width,
										 	tx_mailform_Iattr_cellpadding,
										 	tx_mailform_Iattr_cellspacing,
										 	tx_mailform_Iattr_summary,
										 	tx_mailform_Iattr_border  {
										 		
	private $table_rows = array();
	
	
	// cell content
	//@deprecated
	private $cellContent;
	
	public function __construct() {
		parent::__construct();
		$this->addAttribute(new tx_mailform_attr_summary("none"));
	}
	
	public function setCell($row, $col, $content) {
		$this->cellContent[$row][$col]['content'] = $content;
	}
	
	/**
	 * Setze Tabellen Breite. Zahl oder 0%-100% (String)
	 *
	 * @param unknown_type $height
	 */
	public function setHeight($height) {
		$this->attributes->addAttribute(new tx_mailform_attr_height($height));
	}
	
	/**
	 * Get Height
	 *
	 * @return Object
	 */
	public function getHeight() {
		return $this->attributes->getAttribute('tx_mailform_attr_height');
	}
	
	/**
	 * Setze Tabellen Breite. Zahl oder 0%-100% (String)
	 *
	 * @param Mixed $width
	 */
	public function setWidth($width) {
		$this->attributes->addAttribute(new tx_mailform_attr_width($width));
	}
	
	/**
	 * Get Width
	 *
	 * @return Object
	 */
	public function getWidth() {
		return $this->attributes->getAttribute('tx_mailform_attr_width');
	}
	
	/**
	 * Setze Border
	 *
	 * @param Boolean $border
	 */
	public function setBorder($border) {
		$this->attributes->addAttribute(new tx_mailform_attr_border($border));
	}
	
	/**
	 * Get Border
	 *
	 * @return Object
	 */
	public function getBorder() {
		return $this->attributes->getAttribute('tx_mailform_attr_border');
	}

	/**
	 * Setze Summary
	 *
	 * @param String $summary
	 */
	public function setSummary($summary) {
		$this->attributes->addAttribute(new tx_mailform_attr_summary($summary));
	}
	
	/**
	 * Get Summary
	 *
	 * @return Object
	 */
	public function getSummary() {
		return $this->getAttribute('tx_mailform_attr_summary');
	}
	
	/**
	 * Setze Cellpadding
	 *
	 * @param int $cellpadding
	 */
	public function setCellpadding($cellpadding) {
		$this->addAttribute(new tx_mailform_attr_cellpadding($cellpadding));
	}
	
	/**
	 * Gibt das Objekt von Cellpadding zur�ck
	 *
	 * @return Object
	 */
	public function getCellpadding() {
		return $this->getAttribute('tx_mailform_attr_cellpadding');
	}
	
	/**
	 * Setze Cellspacing (Integer)
	 *
	 * @param int $cellspacing
	 */
	public function setCellspacing($cellspacing) {
		$this->addAttribute(new tx_mailform_attr_cellspacing($cellspacing));
	}
	
	/**
	 * Gibt das Objekt von Cellspacing zur�ck
	 *
	 * @return Object
	 */
	public function getCellspacing() {
		return $this->getAttribute('tx_mailform_attr_cellspacing');
	}


	public function addRow($row) {
		if($row instanceof tx_mailform_tr)
		$this->table_rows[] = $row;
		else throw new Exception('Given Argument is Invalid. Row Object tx_mailform_tr expected.');
	}
	
	public function getRow($index) {
		return $this->table_rows[$index];
	}
	
	/**
	 * Gibt die generierte Tabelle zur�ck
	 *
	 * @return String
	 */
	public function getElementRendered() {
		$this->setTableHeader();
		
		foreach($this->table_rows as $row) {
			$this->appendString($row->getElementRendered());
		}
		
		$this->setTableFooter();
		return $this->getResult();
	}
	
	/**
	 * Create <table [attributes]>
	 * Set the Attributes before creating the table
	 *
	 */
	private function setTableHeader() {
		$this->appendString("\n<!-- Layout Generator Table -->\n<table");
		$this->appendString($this->attributes->getAttributes());
		$this->appendString(">\n");
	}
	
	private function setTableFooter() {
		$this->appendString("</table>\n<!-- Layout Generator Table End -->\n");
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/layout/table/class.tx_mailform_table.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/layout/table/class.tx_mailform_table.php']);
}
?>
