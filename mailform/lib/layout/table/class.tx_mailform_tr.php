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
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_cssclass.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/interface.tx_mailform_Iattr_style.php");

require_once(t3lib_extMgm::extPath('mailform')."lib/layout/table/class.tx_mailform_td.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/interface/class.tx_mailform_parent.php");

/**
 * tx_mailform_tr
 * 
 * Supports creating a table
 *
 * @version 1.0
 * @author Sebastian Winterhalder (sebi@concastic.ch)
 * 
 */
class tx_mailform_tr extends tx_mailform_parent implements tx_mailform_I_layout
{
	private $cells;
	
	/**
	 * F�gt Zeilen Inhalte hinzu
	 *
	 * @param unknown_type $tdObj
	 */
	public function addTd($tdObj) {
		if(!($tdObj instanceof tx_mailform_td))
			throw new Exception('Wrong Argument. Type tx_mailform_td required.');
		$this->cells[] = $tdObj;
	}
	
	public function getCell($index) {
		return $this->cells[$index];
	}
	
	public function countCells() {
		return count($this->cells);
	}
	
	/**
	 * Gibt das Generierte HTML Element aus
	 *
	 */
	public function getElementRendered() {
		if(!empty($this->comment))
			$this->appendString("\n<!-- Class TR: ".$this->comment." -->\n");
		$this->appendString("<tr");
		$this->appendString($this->attributes->getAttributes());
		$this->appendString(">\n");
		
		if(empty($this->cells))
			$this->cells = array();
		
		foreach($this->cells as $key => $cell) {
			$this->appendString($cell->getElementRendered());
		}
		$this->appendString("</tr>\n");
		if(!empty($this->comment))
			$this->appendString("\n<!-- END Class TR: ".$this->comment." -->\n");
		return $this->getResult();
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/layout/table/class.tx_mailform_tr.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/layout/table/class.tx_mailform_tr.php']);
}
?>