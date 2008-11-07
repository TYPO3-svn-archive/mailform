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
* mailform module
*
* @author       Sebastian Winterhalder <sw@internetgalerie.ch>
*/
require_once(t3lib_extMgm::extPath('mailform')."lib/database/class.tx_mailform_db_mailsOfForm.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/database/class.tx_mailform_db_ttContentRow.php");
require_once(t3lib_extMgm::extPath('mailform')."formTypesModel/class.tx_mailform_form.php");

class tx_mailform_fieldGenerator
{
	private $fieldDefinition;
	private $titleRow = array();
	private $mailRow = array();
	private $fieldRowAssoc;
	private $cols = 0;
  
	public function __construct() {
		// For that the right Rows are selected, you have to set $_GET['elmId']
		$this->fieldRowAssoc = t3lib_div::xml2array(tx_mailform_db_ttContentRow::getInstance()->getConfigData());
	
		// Create title row
		$this->titleRow = $this->createFieldRow();
		$cols = count($this->titleRow);
	}

	public function getTitleRow() {
		return $this->titleRow;
	}

	public function getMailRows() {
		return $this->mailRow;
	}
  
	public function getCols()  {
		return $this->cols;
	}

	public function generateContentRows() {
		$rows = tx_mailform_db_mailsOfForm::getInstance()->getRows();
		foreach($rows as $row) {
			$this->addRow($row);
		}
	}
  
	/**
	 *
	 *@param Array with id
	 */        
	public function generateContentRow($mailid = array()) {
		foreach($mailid as $index) {
			$this->addRow(array('mailid' => $index));
		}
	}

	private function addRow($dbMailRow) {
  		if(t3lib_extMgm::isLoaded('mailform_statistics')) {
			$mailRow = $this->createFieldRow();
			$sql = "SELECT * FROM tx_mailformstatistics_stats WHERE mailid = '".$dbMailRow['mailid']."'";

			$res = $GLOBALS['TYPO3_DB']->sql_query($sql);
			
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$index = $this->searchFieldRow($row['ufid'], $mailRow);
				if($index >= 0)
					$mailRow[$index]->getForm()->setupCurrentContent($row);
			}
			$this->mailRow[] = $mailRow;
		}
	}

	private function searchFieldRow($ufid, $fieldArray) {
		for($x = 0; $x < sizeof($fieldArray); $x++) {
			if($fieldArray[$x]->getForm()->getUFID() == $ufid) {
				return $x;
			}
		}
		return -1;
	}
  
	private function createFieldRow() {
		$fieldArray = array();
		foreach($this->fieldRowAssoc['mailform_forms'] as $pageIndex => $page) {
			foreach($page as $fieldIndex => $field) {
				$formInstance = t3lib_div::makeInstance('tx_mailform_form');
				$formInstance->setupForm($field, null, null);
				//$formInstance->getForm()->setFEPageVars($pageIndex, $fieldIndex);
	
				if($formInstance->getForm()->isFieldInStats())
					$fieldArray[] = $formInstance;
			}
		}
		
		return $fieldArray;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/class.tx_mailform_fieldGenerator.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/class.tx_mailform_fieldGenerator.php']);
}

?>