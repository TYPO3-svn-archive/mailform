<?php
session_start();
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

require_once(t3lib_extMgm::extPath('mailform').'tt_content_tx_mailform_config/model/class.tx_mailform_field.php');
require_once(t3lib_extMgm::extPath('mailform').'lib/layout/table/class.tx_mailform_table.php');
require_once(t3lib_extMgm::extPath('mailform').'lib/layout/table/class.tx_mailform_tr.php');

/**
 *
 *
 *
 */   
class  tx_mailform_wizDisplay {
	
	private $P;
	private $fields = array(array());
	private $page = 0;
	public static $GET_vars = array('addCol', 'addRow', 'delRow', 'delCol', 'mrgCellDrow', 'mrgCellDcol', 'mrgCellLrow', 'mrgCellLcol');
	private $currentField;
	
	/**
	 * Initializes the Display
	 * @return	void
	 */
	function __construct()	{
		$this->P = t3lib_div::_GP('P');
		$this->loadConfiguration();
		if(isset($_GET['addCol']))
			$this->addCol($_GET['addCol']);
		if(isset($_GET['addRow']))
			$this->addRow($_GET['addRow']);
		if(isset($_GET['delCol']))
			$this->removeCol($_GET['delCol']);
		if(isset($_GET['delRow']))
			$this->removeRow($_GET['delRow']);
		if(isset($_GET['mrgCellLrow']) && isset($_GET['mrgCellLcol']))
			$this->mergeCellLeft($_GET['mrgCellLrow'], $_GET['mrgCellLcol']);
		if(isset($_GET['mrgCellDcol']) && isset($_GET['mrgCellDrow']))
			$this->mergeCellDown($_GET['mrgCellDrow'], $_GET['mrgCellDcol']);

		$this->displayFieldArray();
		$this->saveConfiguration();
	}
	
	public function createHTML() {
		require_once(t3lib_extMgm::extPath('mailform')."lib/class.tx_mailform_urlHandler.php");
		$urlHandler = new tx_mailform_urlHandler();
		$getVars = tx_mailform_wizDisplay::$GET_vars;
		$table = new tx_mailform_table();
		$table->setWidth("100%");
		$table->setBorder(false);
		$table->setCellpadding(0);
		$table->setCellspacing(0);
		$table->addStyle("border-collapse:collapse;");
		
		// Table Row
		$row = new tx_mailform_tr();
		$cell = new tx_mailform_td();
		$cell->addStyle('border: 1px solid #777; background-color: #bdc0d8;');
		$cell->setAlign('center');
		$cell->setContent('
								<a style="color: #e80303;" href="'.$urlHandler->getCurrentUrl($getVars,true).'&addCol=0"><img src="../gfx/insert_col_top.gif" alt="Insert Col" border="0"></a>
								');
		$row->addTd($cell);
		
		for($x = 0; $x < $this->determineMaxColspan(); $x++) {
			$cell = new tx_mailform_td();
			$cell->addStyle('border: 1px solid #777; background-color: #bdc0d8;');
			$cell->setAlign('center');
			$urlHandler = new tx_mailform_urlHandler();
			$cell->setContent('<a style="color:#0c9d9f;" href="'.$urlHandler->getCurrentUrl($getVars,true).'&delCol='.$x.'"><img src="../gfx/remove_col_top.gif" alt="Remove Col" border="0"></a>
								<a style="color: #e80303;" href="'.$urlHandler->getCurrentUrl($getVars,true).'&addCol='.$x.'"><img src="../gfx/insert_col_top.gif" alt="Insert Col" border="0"></a>
								');
			$row->addTd($cell);
		}
		
		$cell = new tx_mailform_td();
		$cell->addStyle('border: 1px solid #777; background-color: #bdc0d8;');
		$cell->setContent('&nbsp;');
		$row->addTd($cell);
		$table->addRow($row);

		
		
		foreach($this->fields as $rowKey => $rows) {
			$row = new tx_mailform_tr();
			
			$cell = new tx_mailform_td();
			$cell->addStyle('border: 1px solid #777; background-color: #bdc0d8; text-align:center; vertical-align:middle;');
			$cell->setContent('<a href="'.$urlHandler->getCurrentUrl($getVars,true).'&addRow='.$rowKey.'"><img src="../gfx/insert_row_left.gif" alt="Insert Row" border="0"></a>
									<a href="'.$urlHandler->getCurrentUrl($getVars,true).'&delRow='.$rowKey.'"><img src="../gfx/remove_row_left.gif" alt="Remove Row" border="0"></a>
			');
			$cell->setRowspan(1);
			$cell->setWidth(16);
			$row->addTd($cell);
			
			foreach($rows as $field) {
				if(!$field->isPlaceholder()) {
					$cell = new tx_mailform_td();
					$cell->setRowspan($field->getRowspan());
					$cell->setColspan($field->getColspan());
					$cell->setContent($field->getElementRendered());
					$cell->addStyle('border: 1px solid #444;');
					$row->addTd($cell);
				}
			}
			
			$cell = new tx_mailform_td();
			$cell->addStyle('border: 1px solid #777; background-color: #bdc0d8; text-align:center; vertical-align:middle;');
			$cell->setContent('<a href="'.$urlHandler->getCurrentUrl($getVars,true).'&addRow='.$rowKey.'"><img src="../gfx/insert_row.gif" alt="Insert Row" border="0"></a>
									<a href="'.$urlHandler->getCurrentUrl($getVars,true).'&delRow='.$rowKey.'"><img src="../gfx/remove_row.gif" alt="Remove Row" border="0"></a>
			');
			$cell->setRowspan(1);
			$cell->setWidth(16);
			$row->addTd($cell);
			
			$table->addRow($row);
		}
		
		
		
		// Table Row
		$row = new tx_mailform_tr();
		$cell = new tx_mailform_td();
		$cell->addStyle('border: 1px solid #777; background-color: #bdc0d8;');
		
		$cell->setContent('<a style="color: #e80303;" href="'.$urlHandler->getCurrentUrl($getVars,true).'&addCol=0"><img src="../gfx/insert_col.gif" alt="Add Col" border="0"></a>
								');
		$row->addTd($cell);
		
		for($x = 0; $x < $this->determineMaxColspan(); $x++) {
			$cell = new tx_mailform_td();
			$cell->addStyle('border: 1px solid #777; background-color: #bdc0d8;');
			$cell->setAlign('center');
			$urlHandler = new tx_mailform_urlHandler();
			$cell->setContent('<a style="color:#0c9d9f;" href="'.$urlHandler->getCurrentUrl($getVars,true).'&delCol='.$x.'"><img src="../gfx/remove_col.gif" alt="Remove Col" border="0"></a>
									<a style="color: #e80303;" href="'.$urlHandler->getCurrentUrl($getVars,true).'&addCol='.$x.'"><img src="../gfx/insert_col.gif" alt="Add Col" border="0"></a>
				');
			$row->addTd($cell);
		}
		
		$cell = new tx_mailform_td();
		$cell->addStyle('border: 1px solid #777; background-color: #bdc0d8;');
		$cell->setContent('');
		$row->addTd($cell);
		$table->addRow($row);
		
		return $table->getElementRendered();
	}
	
	private function addCol($colIndex) {
		foreach ($this->fields as $rowKey => $row) {
			for($x = count($row)-1; $x >= $colIndex; $x--) {
				$this->fields[$rowKey][$x]->setIndex($rowKey, $x+1);
				$this->fields[$rowKey][$x+1] = $this->fields[$rowKey][$x];
			}
			$field = new tx_mailform_field($rowKey, $colIndex, 1,1);
			$field->setFormElement("New Field");
			$this->fields[$rowKey][$colIndex] = $field;
		}
		ksort($this->fields);
	}
	
	private function addRow($rowIndex) {
		$newArr = array(array());
		for($x = 0; $x < sizeof($this->fields); $x++) {
			if($x >= $rowIndex)
				$newArr[$x+1] = $this->fields[$x];
			else
				$newArr[$x] = $this->fields[$x];
		}
		
		for($x = 0; $x < $this->determineMaxColspan(); $x++) {
			$newArr[$rowIndex][$x] = new tx_mailform_field($rowIndex, $x, 1,1);
		}
		
		foreach($newArr as $row => $cols) {
			foreach($cols as $col => $field) {
				$field->setIndex($row, $col);
			}
		}
		$this->fields = $newArr;
		ksort($this->fields);
	}
	
	private function removeCol($colIndex) {

		$fields = array(array());
		foreach($this->fields as $rowKey => $row) {
			for($x = 0; $x < count($row); $x++) {
				if($x < $colIndex)
					$fields[$rowKey][$x] = $this->fields[$rowKey][$x];
				else {
					if(!empty($this->fields[$rowKey][$x + 1])) {
						$fields[$rowKey][$x] = $this->fields[$rowKey][$x + 1];
						$fields[$rowKey][$x]->setIndex($rowKey, $x);
					}
				}
			}
		}
		
		$this->fields = $fields;
		ksort($this->fields);
	}
	

	private function removeRow($rowIndex) {
		$fields = array(array());
		$c = 0;
		foreach($this->fields as $rowKey => $row) {
			if($rowKey != $rowIndex) {
				$fields[$c] =$row;
				$c++;
			}
		}
		
		foreach($fields as $row => $cols) {
			foreach($cols as $col => $field) {
				$field->setIndex($row, $col);
			}
		}
		$this->fields = $fields;
	}
		
	private function removeCellFromRow($rowIndex, $colIndex) {
		for($x = 0; $x < count($this->fields[$rowIndex]); $x++) {
			if($colIndex <= $x) {
				if(!empty($this->fields[$rowIndex][$x+1])) {
					$this->fields[$rowIndex][$x] = $this->fields[$rowIndex][$x+1];
					$this->fields[$rowIndex][$x]->setIndex($rowIndex, $x);
				} else {
					$this->fields->setPlaceholderIndex($rowIndex, $x);
					//unset($this->fields[$rowIndex][$x]);
				}
			}
		}
	}
	
	private function mergeCellDown($rowIndex, $colIndex) {
		$crRowspan = $this->fields[$rowIndex][$colIndex]->getRowspan();
		
		/*
		// Falls die Obere Zeile mehr Colspan als die zu Mergende Zeile hat
		while(!empty($this->fields[$rowIndex+1][$colIndex]) &&
				$this->fields[$rowIndex][$colIndex]->getColspan() > $this->fields[$rowIndex+1][$colIndex]->getColspan()
			) {
			$this->mergeCellLeft($rowIndex+1, $colIndex);
		}
		
		// Falls die Obere Zelle weniger Colspan als die zu Mergende Zelle hat
		while(!empty($this->fields[$rowIndex+1][$colIndex]) && 
				$this->fields[$rowIndex][$colIndex]->getColspan() < $this->fields[$rowIndex+1][$colIndex]->getColspan()) {
			$this->mergeCellLeft($rowIndex, $colIndex);
		}
		*/
		
		if(!empty($this->fields[$rowIndex + $crRowspan][$colIndex]))
			$rs = $this->fields[$rowIndex][$colIndex]->getRowspan() + $this->fields[$rowIndex + $crRowspan][$colIndex]->getRowspan();
		else $rs = $this->fields[$rowIndex][$colIndex]->getRowspan();
		
		$this->fields[$rowIndex][$colIndex]->setRowspan($rs);
		$this->removeCellFromRow($rowIndex + $crRowspan, $colIndex);
		
		if($this->fields[$rowIndex][$colIndex]->getRowspan() >= $this->determineMaxRowspan()) {
			$this->fields[$rowIndex][$colIndex]->setColspan(1);
		}
	}
	
	private function displayFieldArray() {
		
		foreach ($this->fields as $rowIndex => $row ) {
			print "[$rowIndex]=><br>";
			foreach($row as $colIndex => $col) {
				print "&nbsp;&nbsp;&nbsp;[$colIndex] => <br>";
			}
		}
	}
	
	private function mergeCellLeft($rowIndex, $colIndex) {
		/*
		// Passe die Felder Links an
		// Falls das Rechte Col Mehr Felder als die zu Mergende Zelle Rowspan hat
		while(!empty($this->fields[$rowIndex][$colIndex+1]) && 
				$this->fields[$rowIndex][$colIndex]->getRowspan() > $this->fields[$rowIndex][$colIndex+1]->getRowspan()) {

			$this->mergeCellDown($rowIndex, $colIndex+1);
		}
		
		// Falls das rechte Feld mehr Rowspan als das linke hat
		while(!empty($this->fields[$rowIndex][$colIndex+1]) &&
			$this->fields[$rowIndex][$colIndex]->getRowspan() < $this->fields[$rowIndex][$colIndex+1]->getRowspan()) {

			$this->mergeCellDown($rowIndex, $colIndex);	
		}
		
		*/
		$maxCol = $this->getColCount();
		
		if($this->fields[$rowIndex][$colIndex] > 1) {
			for($x = 1; $x <= $this->fields[$rowIndex][$colIndex]->getRowspan(); $x++) {
				$this->mergeCellDown($rowIndex, $colIndex+1);
			}
		}
		
		// Define Colspan
		if(!empty($this->fields[$rowIndex][$colIndex + 1]))
			$cs = $this->fields[$rowIndex][$colIndex]->getColspan() + $this->fields[$rowIndex][$colIndex + 1]->getColspan();
		else $cs = $this->fields[$rowIndex][$colIndex]->getColspan();
		
		if($cs > $maxCol)
		 $cs = $maxCol;
		while( ($this->getColspanSum($rowIndex) > $maxCol) && ($cs > 1))
		 $cs--;
		 
		$this->fields[$rowIndex][$colIndex]->setColspan($cs);
		$this->removeCellFromRow($rowIndex, $colIndex + 1);
		foreach($this->fields as $rowKey => $row) {
			$this->recalculateColspanSUM($rowKey);
		}
	}
	
	private function recalculateColspanSUM($rowIndex) {
		$cs = 0;
		$colCount = $this->getColCount();
		
		while($this->getColspanSum($rowIndex) > $this->determineMaxColspan()) {
			foreach($this->fields[$rowIndex] as $colKey => $field) {				
				if($field->getColspan() > 1) {
					$field->setColspan($field->getColspan() - 1);
				}
			}
		}
	}
	
	
	private function saveConfiguration() {
		
		$sql = "DELETE FROM tx_mailform_pluginConf_temp WHERE pUid = '".$this->P['uid']."'";
		$GLOBALS['TYPO3_DB']->sql_query($sql);

		foreach($this->fields as $rowKey => $fieldRow) {
			foreach($fieldRow as $colKey => $field) {
				$field->saveField($this->P['uid'], $rowKey, $colKey);
			}
		}
	}
	
	private function loadConfiguration() {
		$sql = "SELECT * FROM tx_mailform_pluginConf_temp WHERE pUid = '".$this->P['uid']."'";
		$res = $GLOBALS['TYPO3_DB']->sql_query($sql);
		
		$this->fields = array(array());
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$this->fields[$row['Frow']][$row['Fcol']] = new tx_mailform_field($row['Frow'], $row['Fcol']);
			$this->fields[$row['Frow']][$row['Fcol']]->loadFromAssoc($row);
			$this->fields[$row['Frow']][$row['Fcol']]->setFormElement("Loaded Field");
		}
		
		$keys = array_keys($this->fields);
		for($x = 0; $x < count($keys); $x++) {
			ksort($this->fields[$keys[$x]]);
		}
		ksort($this->fields);
	}

	private function determineMaxColspan() {
		
		$colspan = 1;
		
		foreach($this->fields as $fieldRow) {
			$tmp = 0;
			foreach($fieldRow as $field) {
				if($field->getRowspan() > 1)
					$tmp = $tmp + $field->getColspan();
				else
					$tmp++;
			}
			$colspan = $tmp > $colspan ? $tmp : $colspan;
		}
		
		
		return $colspan;
	}
	
	private function determineMaxRowspan() {
		
		$rowspan = 1;
		
		foreach($this->fields as $fieldRow) {
			$tmpL = 1;
			foreach($fieldRow as $field) {
				if($field->getRowspan() > $tmpL)
					$tmpL = $field->getRowspan();
			}
			$rowspan = $rowspan + $tmpL;
		}
		
		return $rowspan;
	}
	
	private function getColCount() {
		$colCount = 0;
		for ($x = 0; $x < count($this->fields); $x++) {
			if(sizeof($this->fields[$x]) > $colCount)
				$colCount = sizeof($this->fields[$x]);
		}
		return $colCount;
	}
	
	private function getColspanSum($rowIndex) {
		$cs = 0;
		$keys = array_keys($this->fields[$rowIndex]);
		for ($x = 0; $x < count($keys); $x++) {
				$cs = $cs + $this->fields[$rowIndex][$keys[$x]]->getColspan();
		}
		return $cs;
	}
	
}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/tt_content_tx_mailform_config/model/class.tx_mailform_wizDisplay.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/tt_content_tx_mailform_config/model/class.tx_mailform_wizDisplay.php']);
}
?>
