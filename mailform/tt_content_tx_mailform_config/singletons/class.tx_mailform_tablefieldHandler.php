<?php/****************************************************************  Copyright notice**  (c) 2007 Sebastian Winterhalder <sw@internetgalerie.ch>*  All rights reserved**  This script is part of the TYPO3 project. The TYPO3 project is*  free software; you can redistribute it and/or modify*  it under the terms of the GNU General Public License as published by*  the Free Software Foundation; either version 2 of the License, or*  (at your option) any later version.**  The GNU General Public License can be found at*  http://www.gnu.org/copyleft/gpl.html.**  This script is distributed in the hope that it will be useful,*  but WITHOUT ANY WARRANTY; without even the implied warranty of*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the*  GNU General Public License for more details.**  This copyright notice MUST APPEAR in all copies of the script!***************************************************************/require_once(t3lib_extMgm::extPath('mailform').'lib/controller/class.tx_mailform_observer.php');require_once(t3lib_extMgm::extPath('mailform').'tt_content_tx_mailform_config/singletons/class.tx_mailform_configData.php');require_once(t3lib_extMgm::extPath('mailform').'lib/layout/table/class.tx_mailform_table.php');require_once(t3lib_extMgm::extPath('mailform').'tt_content_tx_mailform_config/model/class.tx_mailform_field.php');/** * tx_mailform_tablefieldHandler * * @author Sebastian Winterhalder <sw@internetgalerie.ch> * */class tx_mailform_tablefieldHandler extends tx_mailform_observer {	// class variables	private $P;	private $fields = array(array(array()));	private $currentPage = 0;	private $activatePrinting = false;	private static $selfInstance;	/**	 * Private Constructor (Singleton Holder)	 * Do only create this class over tx_mailform_tablefieldHandler::getInstance();	 *	 */	private function __construct() {		// Initialize variables		$this->P = t3lib_div::_GP('P');		$this->loadFields();
		
		// Methods called when in TYPO3 Backend Modus
		if(TYPO3_MODE == "BE") {			$this->updateIndexes();			$this->urlChanges();			if($this->activatePrinting)				$this->displayFieldArray();
		}	}	/**	 * loadFields();	 *	 */	private function loadFields() {		$conf = tx_mailform_configData::getInstance();		$confData = $conf->getFieldData();
				$c = 0;		foreach($confData as $field) {			$this->fields[$field[tx_mailform_field::$fieldPrefix.'page']][$field[tx_mailform_field::$fieldPrefix.'rowIndex']][$field[tx_mailform_field::$fieldPrefix.'colIndex']] = new tx_mailform_field($field[tx_mailform_field::$fieldPrefix.'rowIndex'],$field[tx_mailform_field::$fieldPrefix.'colIndex'],$field[tx_mailform_field::$fieldPrefix.'page']);			$this->fields[$field[tx_mailform_field::$fieldPrefix.'page']][$field[tx_mailform_field::$fieldPrefix.'rowIndex']][$field[tx_mailform_field::$fieldPrefix.'colIndex']]->loadFromConfig($field);		}		if(class_exists('tx_mailform_formHandler')) {			$tblfHan = tx_mailform_formHandler::getInstance();			$formKeys = array();			foreach($this->fields as $fieldsPage) {				foreach($fieldsPage as $rowField) {					foreach($rowField as $field) {						$formKeys = array_merge($formKeys, $field->getFormElements());					}				}			}			foreach($formKeys as $formUid) {				try {					$form = $tblfHan->getForm($formUid);					$form->getForm()->setFormReferenceUsed(true);				} catch (Exception $e) {					foreach($this->fields as $fieldPage) {						foreach($fieldPage as $rowField) {							foreach($rowField as $field) {								$field->removeAllFormElementWithUID($formUid);							}						}					}				}			}		}	}	/**	 * saveFields();	 *	 */	private function saveFields() {		$arr = array();		foreach($this->fields as $pagekey => $page) {			foreach($page as $rowKey => $row) {				foreach($row as $colKey => $col) {					$arr[] = $col->getConfigData();				}			}		}				$cfg = tx_mailform_configData::getInstance();		$cfg->setFieldData($arr);	}	/**	 * getFields();	 *	 * @return Array	 */	public function getFields() {		return $this->fields;	}	/**	 * static getInstance();	 *	 * @return Object	 */	public static function getInstance() {		if(!isset(tx_mailform_tablefieldHandler::$selfInstance)) {				tx_mailform_tablefieldHandler::$selfInstance = new tx_mailform_tablefieldHandler();		}		return tx_mailform_tablefieldHandler::$selfInstance;	}	/**	 * displayFieldArray()	 *	 */	private function displayFieldArray() {	  $table = new tx_mailform_table();	  $table->addStyle("border: 1px solid #555;");		foreach($this->fields as $pageId => $page) {			$row0 = new tx_mailform_tr();			$td = new tx_mailform_td();			$td->setContent($pageId);			$row0->addTd($td);			$td = new tx_mailform_td();			$tableRow = new tx_mailform_table();			$tableRow->addStyle("border: 1px solid #555;");			foreach($page as $rowId => $row) {				$row1 = new tx_mailform_tr();				$td1 = new tx_mailform_td();				$td1->setContent($rowId);				$td1->addStyle('color: #F00;');				$row1->addTd($td1);				$table2 = new tx_mailform_table();				$table2->addStyle("border: 1px solid #555;");				$td1a = new tx_mailform_td();				foreach($row as $pageId => $page) {				  $row2 = new tx_mailform_tr();				  $td2 = new tx_mailform_td();				  $td2->setContent($pageId);				  $td2->addStyle('color: #F00;');				  $row2->addTd($td2);				  $cont = "";				  $td2a = new tx_mailform_td();					if(is_object($page)) {					  $cont .= "[".$page->getRowIndex()."]";					  $cont .= "[".$page->getColIndex()."]";					  if($page->isPlaceholder()) {					  $pi = $page->getPlaceholderIndex();							$cont .= " PlH".$pi[0]." ".$po[1];						}						$td2a->setContent($cont);					} else {						$td2a->setContent($page);					}					$row2->addTd($td2a);					$table2->addRow($row2);				}				$td1a->setContent($table2->getElementRendered());        $row1->addTd($td1a);				$tableRow->addRow($row1);			}			$td->setContent($tableRow->getElementRendered());			$row0->addTd($td);			$table->addRow($row0);		}	  print $table->getElementRendered();	}	/**	 * urlChanges();	 *	 *	 */	private function urlChanges() {
		$this->ensureStatus("be"); // Function only in BE allowed		$sH = tx_mailform_saveState::getInstance();		if(isset($_POST['mailform_addField']) && isset($_POST['mailform_addFieldRow']) && isset($_POST['mailform_addFieldCol'])) {			$sH->setChanged(true);			if(gettype($this->fields[$_POST['mailform_currentPage']][$_POST['mailform_addFieldRow']][$_POST['mailform_addFieldCol']]) == 'object')				$this->fields[$_POST['mailform_currentPage']][$_POST['mailform_addFieldRow']][$_POST['mailform_addFieldCol']]->addFormElement($_POST['mailform_addField'], $_POST['mailform_addFieldInnerPosi']);			$this->saveFields();		}		if(isset($_GET['movFormIndex']) && isset($_GET['movFormTo']) && isset($_GET['fMdr']) && isset($_GET['fMdc'])) {      		$this->fields[$_GET['page']][$_GET['fMdr']][$_GET['fMdc']]->moveFormInField($_GET['movFormIndex'],$_GET['movFormTo']);			$sH->setChanged(true);			$this->saveFields();		}		if(isset($_GET['page']) && isset($_GET['fAdc']) && isset($_GET['fAdr']) && isset($_GET['sField'])) {			$this->fields[$_GET['page']][$_GET['fAdr']][$_GET['fAdc']]->addFormElement($_GET['sField'], $_GET['sFieldIndex']);			$sH->setChanged(true);			$this->saveFields();		}		if(isset($_GET['delFfromF']) && isset($_GET['fAdr']) && isset($_GET['fAdc'])) {			if(isset($_GET['formPage']))				$this->currentPage = $_GET['formPage'];			$this->removeFormFromField($_GET['delFfromF'], $this->currentPage, $_GET['fAdr'], $_GET['fAdc']);			$sH->setChanged(true);			$this->saveFields();		}		if(isset($_POST['mailform_fieldConf_col']) && isset($_POST['mailform_fieldConf_row']) && isset($_POST['mailform_fieldConf_page'])) {			$this->fields[$_POST['mailform_fieldConf_page']][$_POST['mailform_fieldConf_row']][$_POST['mailform_fieldConf_col']]->setCssClass($_POST['mailform_fieldConf_cssclass']);			$this->fields[$_POST['mailform_fieldConf_page']][$_POST['mailform_fieldConf_row']][$_POST['mailform_fieldConf_col']]->setHeight($_POST['mailform_fieldConf_height']);			$this->fields[$_POST['mailform_fieldConf_page']][$_POST['mailform_fieldConf_row']][$_POST['mailform_fieldConf_col']]->setWidth($_POST['mailform_fieldConf_width']);			$this->fields[$_POST['mailform_fieldConf_page']][$_POST['mailform_fieldConf_row']][$_POST['mailform_fieldConf_col']]->setCondition($_POST['mailform_fieldConf_condition']);
			$this->fields[$_POST['mailform_fieldConf_page']][$_POST['mailform_fieldConf_row']][$_POST['mailform_fieldConf_col']]->setConditionActivated($_POST['mailform_fieldConf_activateCondition']);
			$this->saveFields();		}	}	/**	 * removeFormFromField($formUName, $page=-1, $row=-1, $col=-1)	 *	 * @param String $formUName	 * @param Int $page	 * @param Int $row	 * @param Int $col	 */	public function removeFormFromField($formUName, $page=-1, $row=-1, $col=-1) {
		$this->ensureStatus("be"); // Function only in BE allowed
				if($page == -1) {			// Remove GLOBAL From all Pages, Rows, and Fields			foreach($this->fields as $page) {				foreach($page as $row) {					foreach($row as $col) {						$innerKeys = array_keys($col->getFormElements(), $formUName);						foreach($innerKeys as $innerKey)							$col->removeFormElement($innerKey);						}					}				}			}		elseif($row == -1 ) {			// Remove all Forms from a specified page			if($page < 0)				throw new Exception('Parameter $page is expected to be greater than zero!');			foreach($this->fields[$page] as $row) {				foreach($row as $col) {					$innerKeys = array_keys($col->getFormElements(), $formUName);					foreach($innerKeys as $innerKey)						$col->removeFormElement($innerKey);				}			}		}		elseif($col == -1) {			// Remove all Forms from a specified row of a page			if($page < 0 || $row < 0)				throw new Exception('Parameter $page: '.$page.' and $row: '.$row.' is expected to be greater than zero!');			foreach($this->fields[$page][$row] as $col) {				$innerKeys = array_keys($col->getFormElements(), $formUName);				foreach($innerKeys as $innerKey)					$col->removeFormElement($innerKey);			}		} else {			// Remove all Forms from a specified field			$this->fields[$page][$row][$col]->removeFormElement($formUName);		}				$sH = tx_mailform_saveState::getInstance();		$sH->setChanged(true);		$this->saveFields();	}	public function addCol($colIndex) {		$this->ensureStatus("be"); // Function only in BE allowed		$this->makeArrayClear();		$oldMaxColspan = $this->determineMaxColspan();		if($colIndex < 0) {			// Add a Line on 0			foreach($this->fields[$this->currentPage] as $rowKey => $row) {				// Move all Fields to the right, after colIndex        for($x = $oldMaxColspan-1; $x >= 0; $x--) {					$this->fields[$this->currentPage][$rowKey][$x+1] = $this->fields[$this->currentPage][$rowKey][$x];				}				$newObj = new tx_mailform_field($rowKey, $colIndex+1, $this->currentPage, 1,1);				$this->fields[$this->currentPage][$rowKey][$colIndex+1] = $newObj;			}		}			else		{		  // Add a line on 1 or higher		  foreach($this->fields[$this->currentPage] as $rowKey => $row) {				// Move all Fields to the right, after colIndex        for($x = $oldMaxColspan-1; $x > $colIndex; $x--) {					$this->fields[$this->currentPage][$rowKey][$x+1] = $this->fields[$this->currentPage][$rowKey][$x];				}				$newObj = new tx_mailform_field($rowKey, $colIndex+1, $this->currentPage, 1,1);				$this->fields[$this->currentPage][$rowKey][$colIndex+1] = $newObj;				$plIndex = $this->fields[$this->currentPage][$rowKey][$colIndex]->isPlaceholder() ? $this->fields[$this->currentPage][$rowKey][$colIndex]->getPlaceholderIndex() : false;			}		}		$this->updateIndexes();		$sH = tx_mailform_saveState::getInstance();		$sH->setChanged(true);		$this->saveFields();	}	/**	 * updateColspans()	 *	 * @version 0.8.3	 */	public function updateColspans() {		$fields = $this->fields;		foreach($fields[$this->currentPage] as $rowKey => $row) {			foreach($row as $fieldKey => $field) {				if($field->getColspan() > 1) {					$count = $field->getColspan();					$index = array($field->getRowIndex(), $field->getColIndex());				}				else {					if($count > 1) {						$field->setPlaceholderIndex($index[0], $index[1], $this->currentPage);						$formsInField = $field->getFormElements();						foreach($formsInField as $formKeyIndex => $formKey) {							$fields[$this->currentPage][$index[0]][$index[1]]->addFormElement($formKey);						}						for($x = sizeof($formsInField)-1; $x >= 0 ; $x--) {							$field->removeFormElement($x);						}						$count--;					} else {						$field->unsetPlaceholder();					}				}			}		}		$this->fields = $fields;	}	/**	 * addRow($rowIndex)	 *	 * @param int $rowIndex	 */	public function addRow($rowIndex) {		$this->ensureStatus("be"); // Function only in BE allowed		$maxColspan = $this->determineMaxColspan();		if($rowIndex < 0) {			// Add a new first line			for($x = $this->determineMaxRowspan(); $x >= 0; $x--) {				$this->fields[$this->currentPage][$x] = $this->fields[$this->currentPage][$x-1];			}			$arr = array(array());			for($x = 0; $x < $maxColspan; $x++) {				$arr[$x] = new tx_mailform_field($rowIndex+1,$x,$this->currentPage,1,1);			}			$this->fields[$this->currentPage][0] = $arr;		} else {			for($x = $this->determineMaxRowspan(); $x > $rowIndex; $x--) {				$this->fields[$this->currentPage][$x] = $this->fields[$this->currentPage][$x-1];			}			$arr = array(array());			for($x = 0; $x < $maxColspan; $x++) {				$arr[$x] = new tx_mailform_field($rowIndex+1,$x,$this->currentPage,1,1);			}			$this->fields[$this->currentPage][$rowIndex+1] = $arr;		}		$this->updateIndexes();		$sH = tx_mailform_saveState::getInstance();		$sH->setChanged(true);		$this->saveFields();	}	/**	 * updateIndexes();	 *	 */	private function updateIndexes() {		for($page = 0; $page < count($this->fields); $page++) {			for($rowIndex = 0; $rowIndex < count($this->fields[$page]); $rowIndex++) {				for($colIndex = 0; $colIndex < count($this->fields[$page][$rowIndex]); $colIndex++) {					if(!empty($this->fields[$page][$rowIndex][$colIndex])) {						$this->fields[$page][$rowIndex][$colIndex]->setIndex($rowIndex, $colIndex);						$this->fields[$page][$rowIndex][$colIndex]->setPage($page);					}				}			}		}	}	/**	 * makeArrayClear();	 *	 */	private function makeArrayClear() {
		$this->ensureStatus("be"); // Function only in BE allowed
				$field = array();		$p = $f = $r = 0;		foreach($this->fields as $pageKey => $page) {			$r = 0;			foreach($page as $rowKey => $row) {				$f = 0;				foreach($row as $colKey => $col) {					$field[$p][$r][$f] = $col;					$f++;				}				$r++;			}			$p++;		}		$this->fields = $field;		$this->saveFields();	}	/**	 * removeCol()	 *	 *	 */	public function removeCol($colIndex) {
		$this->ensureStatus("be"); // Function only in BE allowed
				$fields = array(array());		foreach($this->fields[$this->currentPage] as $rowKey => $row) {			// Durchlaufe die Spalten in der Zeile Row				// Vorhaltige Zelländerungen				// Vor dem verschieben einige Faktoren ändern: colspan und placeholder				if($this->fields[$this->currentPage][$rowKey][$colIndex]->getColspan() > 1) {					if(!empty($this->fields[$this->currentPage][$rowKey][$colIndex+1])) {						$this->fields[$this->currentPage][$rowKey][$colIndex+1]->setColspan($this->fields[$this->currentPage][$rowKey][$colIndex]->getColspan()-1);						$this->fields[$this->currentPage][$rowKey][$colIndex+1]->unsetPlaceholder();						for($x = ($colIndex+2); $x < $this->fields[$this->currentPage][$rowKey][$colIndex]->getColspan()-1; $x++) {							if(!empty($this->fields[$this->currentPage][$rowKey][$x]))								$this->fields[$this->currentPage][$rowKey][$x]->setPlaceholderIndex($rowKey, ($colIndex+1), $this->currentPage);						}					}				}				// decrease colspan of placeholder-main element				if($this->fields[$this->currentPage][$rowKey][$colIndex]->isPlaceholder()) {					$plIndex = $this->fields[$this->currentPage][$rowKey][$colIndex]->getPlaceholderIndex();					$this->fields[$this->currentPage][$plIndex[0]][$plIndex[1]]->setColspan($this->fields[$this->currentPage][$plIndex[0]][$plIndex[1]]->getColspan()-1);				}			// Move the elements			for($x = 0; $x < count($row); $x++) {				// vor dem Selektierten colIndex soll nichts passieren				if($x < $colIndex) {					$fields[$rowKey][$x] = $this->fields[$this->currentPage][$rowKey][$x];				}				else {					// Nach dem Selektierten colIndex soll verschoben werden					if(!empty($this->fields[$this->currentPage][$rowKey][$x + 1])) {						$fields[$rowKey][$x] = $this->fields[$this->currentPage][$rowKey][$x + 1];						$fields[$rowKey][$x]->setIndex($rowKey, $x);					}				}			}		}		$this->fields[$this->currentPage] = $fields;		ksort($this->fields[$this->currentPage]);		$this->updateIndexes();		$sH = tx_mailform_saveState::getInstance();		$sH->setChanged(true);		$this->saveFields();	}	/**	 * setCurrentPage($currentPage)	 *	 * @param int $currentPage	 */	public function setCurrentPage($currentPage) {		$this->currentPage = $currentPage;	}	/**	 * removeRow($rowIndex)	 *	 * @param unknown_type $rowIndex	 */	public function removeRow($rowIndex) {
		$this->ensureStatus("be"); // Function only in BE allowed
				$fields = array(array());		$c = 0;		foreach($this->fields[$this->currentPage] as $rowKey => $row) {			if($rowKey != $rowIndex) {				$fields[$c] =$row;				$c++;			}		}		foreach($fields as $row => $cols) {			foreach($cols as $col => $field) {				$field->setIndex($row, $col);			}		}		$this->fields[$this->currentPage] = $fields;		$sH = tx_mailform_saveState::getInstance();		$sH->setChanged(true);		$this->saveFields();	}	private $internalCounter = 0;	private $loopProtection = 20;	private $count;	/**	 * updateIntegrity()	 *	 */	public function updateIntegrity() {		$this->ensureStatus("be"); // Function only in BE allowed
		
		foreach($this->fields[$this->currentPage] as $row) {			foreach($row as $col) {			}		}	}	/**	 * mergeCellDown($rowIndex, $colIndex)	 *	 * @param Int $rowIndex	 * @param Col $colIndex	 * @return Boolean	 */	public function mergeCellDown($rowIndex, $colIndex) {
		$this->ensureStatus("be"); // Function only in BE allowed
				if($this->internalCounter > $this->loopProtection) {			return false;		} else {			$this->internalCounter++;		}		$currentRowspan = $this->fields[$this->currentPage][$rowIndex][$colIndex]->getRowspan();		$currentColspan = $this->fields[$this->currentPage][$rowIndex][$colIndex]->getColspan();		if(!empty($this->fields[$this->currentPage][$rowIndex+$currentRowspan][$colIndex])			&& $this->fields[$this->currentPage][$rowIndex+$currentRowspan][$colIndex]->isPlaceholder()) {			$plIndex = $this->fields[$this->currentPage][$rowIndex+$currentRowspan][$colIndex]->getPlaceholderIndex();			for($x = $plIndex[1]; $x < $colIndex; $x++) {				if($this->activatePrinting)					print "mCD0: Merge Cell Left: Row: $rowIndex -".$x."<br>";				$this->count['mcd'][0]++;				$this->mergeCellLeft($rowIndex, $x);			}			$colIndex = $plIndex[1];		}		$currentRowspan = $this->fields[$this->currentPage][$rowIndex][$colIndex]->getRowspan();		$currentColspan = $this->fields[$this->currentPage][$rowIndex][$colIndex]->getColspan();		// Falls die Obere Zeile mehr Colspan als die zu Mergende Zeile hat		while(!empty($this->fields[$this->currentPage][$rowIndex+$currentRowspan][$colIndex]) &&				$currentColspan > $this->fields[$this->currentPage][$rowIndex+$currentRowspan][$colIndex]->getColspan()			) {			if($this->activatePrinting)				print "mCD1: Merge Cell Left: Row: $rowIndex+$currentRowspan -".$colIndex."<br>";			$this->count['mcd'][1]++;			$this->mergeCellLeft($rowIndex+$currentRowspan, $colIndex);		}		// Falls die Obere Zelle weniger Colspan als die zu Mergende Zelle hat		while(!empty($this->fields[$this->currentPage][$rowIndex+$currentRowspan][$colIndex]) &&				$this->fields[$this->currentPage][$rowIndex][$colIndex]->getColspan() < $this->fields[$this->currentPage][$rowIndex+$currentRowspan][$colIndex]->getColspan()				&& !$this->fields[$this->currentPage][$rowIndex+$currentRowspan][$colIndex]->isPlaceholder()				) {			$this->count['mcd'][2]++;			if($this->activatePrinting)				print "mCD2: Merge Cell Left: Row: $rowIndex -".$colIndex."<br>";			$this->mergeCellLeft($rowIndex, $colIndex);		}		// Setze alle 'unsichtbaren' felder weiter unten mit Referenz auf das sichtbare		if(!empty($this->fields[$this->currentPage][$rowIndex+$currentRowspan][$colIndex]) && $this->fields[$this->currentPage][$rowIndex+$currentRowspan][$colIndex]->getRowspan() > 1) {			for($x = $rowIndex; $x <= $rowIndex+$currentRowspan+$this->fields[$this->currentPage][$rowIndex+$currentRowspan][$colIndex]->getRowspan()-1; $x++) {				for($y = $colIndex; $y <= $colIndex+$currentColspan-1; $y++) {					if(!$rowIndex+$x == $rowIndex && $colIndex == $y)					if(!empty($this->fields[$x][$y]))						$this->fields[$x][$y]->setPlaceholderIndex($rowIndex, $colIndex, $this->currentPage);				}			}		}		// Falls nur ein Feld gemerged wird, wird diese Funktion ausgeführt		if(!empty($this->fields[$this->currentPage][$rowIndex+$currentRowspan][$colIndex])) {			$this->fields[$this->currentPage][$rowIndex+$currentRowspan][$colIndex]->setPlaceholderIndex($rowIndex, $colIndex, $this->currentPage);		}		if(!empty($this->fields[$this->currentPage][$rowIndex + $currentRowspan][$colIndex]))			$rs = $currentRowspan + $this->fields[$this->currentPage][$rowIndex + $currentRowspan][$colIndex]->getRowspan();		else $rs = $this->fields[$this->currentPage][$rowIndex][$colIndex]->getRowspan();		$this->fields[$this->currentPage][$rowIndex][$colIndex]->setRowspan($rs);		$sH = tx_mailform_saveState::getInstance();		$sH->setChanged(true);		$this->saveFields();		return true;	}	/**	 * mergeCellLeft($rowIndex, $colIndex, $recursive=false)	 *	 * @param Int $rowIndex	 * @param Int $colIndex	 * @param Boolean $recursive	 * @return Boolean	 */	public function mergeCellLeft($rowIndex, $colIndex, $recursive=false) {
		$this->ensureStatus("be"); // Function only in BE allowed
				if($this->internalCounter > $this->loopProtection) {			return false;		} else {			$this->internalCounter++;		}				$currentColspan = $this->fields[$this->currentPage][$rowIndex][$colIndex]->getColspan();		$currentRowspan = $this->fields[$this->currentPage][$rowIndex][$colIndex]->getRowspan();		// Falls das Rechte feld Oberhalb dem aktuellen Feld anfängt		if(!empty($this->fields[$this->currentPage][$rowIndex][$colIndex+$currentColspan]) &&				$this->fields[$this->currentPage][$rowIndex][$colIndex+$currentColspan]->isPlaceholder()			)		{			$plIndex = $this->fields[$this->currentPage][$rowIndex][$colIndex+$currentColspan]->getPlaceholderIndex();			for($x = $plIndex[0]; $x < $rowIndex; $x++) {				if($this->activatePrinting)					print "mCL0: Merge Cell Down: Row: $x -".$colIndex."<br>";				$this->count['mcl'][0]++;				$this->mergeCellDown($x, $colIndex);			}			$rowIndex = $plIndex[0];		}				// Passe die Felder Links an		// Falls das Rechte Col Mehr Felder als die zu Mergende Zelle Rowspan hat		while(!empty($this->fields[$this->currentPage][$rowIndex][$colIndex+$currentColspan]) &&				$this->fields[$this->currentPage][$rowIndex][$colIndex]->getRowspan() > $this->fields[$this->currentPage][$rowIndex][$colIndex+$currentColspan]->getRowspan()				&& !$this->fields[$this->currentPage][$rowIndex][$colIndex+$currentColspan]->isPlaceholder()				) {			$this->count['mcl'][1]++;			if($this->activatePrinting)				print "mCL1: Merge Cell Down: Row: $rowIndex -".$colIndex+$currentColspan."<br>";			$this->mergeCellDown($rowIndex, $colIndex+$currentColspan);		}		// Falls das rechte Feld mehr Rowspan als das linke hat		while(!empty($this->fields[$this->currentPage][$rowIndex][$colIndex+$currentColspan]) &&			$this->fields[$this->currentPage][$rowIndex][$colIndex]->getRowspan() < $this->fields[$this->currentPage][$rowIndex][$colIndex+$currentColspan]->getRowspan()			&& !$this->fields[$this->currentPage][$rowIndex][$colIndex+$currentColspan]->isPlaceholder()) {			if($this->activatePrinting)				print "mCL2: Merge Cell Down: Row: $rowIndex -".$colIndex."<br>";			$this->count['mcl'][2]++;			$this->mergeCellDown($rowIndex, $colIndex);		}		// Setze den Referenz Index aller Felder		for($rId = $rowIndex; $rId <= $rowIndex+$currentRowspan-1; $rId++) {			for($cId = $colIndex; $cId <= $colIndex+$currentColspan; $cId++) {				if(!($rId == $rowIndex && $cId == $colIndex) && !empty($this->fields[$this->currentPage][$rId][$cId]) ) {					$this->fields[$this->currentPage][$rId][$cId]->setPlaceholderIndex($rowIndex, $colIndex, $this->currentPage);				}			}		}		if(!empty($this->fields[$this->currentPage][$rowIndex][$colIndex+$currentColspan]))			$cs = $this->fields[$this->currentPage][$rowIndex][$colIndex]->getColspan() + $this->fields[$this->currentPage][$rowIndex][$colIndex+$currentColspan]->getColspan();		else			$cs = $this->fields[$this->currentPage][$rowIndex][$colIndex]->getColspan();		$this->fields[$this->currentPage][$rowIndex][$colIndex]->setColspan($cs);		$sH = tx_mailform_saveState::getInstance();		$sH->setChanged(true);		$this->saveFields();		return true;	}	/**	 * splitCellDown($rowIndex, $colIndex)	 *	 * @param Int $rowIndex	 * @param Int $colIndex	 */	public function splitCellDown($rowIndex, $colIndex) {		$this->ensureStatus("be"); // Function only in BE allowed
				if($this->fields[$this->currentPage][$rowIndex][$colIndex]->getColspan() > 1) {			$cs = $this->fields[$this->currentPage][$rowIndex][$colIndex]->getColspan();			$rs = $this->fields[$this->currentPage][$rowIndex][$colIndex]->getRowspan();			for($x = $rowIndex; $x < ($rowIndex + $rs); $x++) {				for($y = $colIndex; $y < ($colIndex + $this->fields[$this->currentPage][$rowIndex][$colIndex]->getColspan()); $y++) {					$pIl = $this->fields[$this->currentPage][$x][$y]->getPlaceholderIndex();					if($y != $colIndex) {						$this->fields[$this->currentPage][$x][$y]->setPlaceholderIndex($x, $pIl[1], $this->currentPage);					}					else {						$this->fields[$this->currentPage][$x][$y]->unsetPlaceholder();					}					$this->fields[$this->currentPage][$x][$y]->setRowspan(1);					$this->fields[$this->currentPage][$x][$y]->setColspan($cs);				}			}		} else {			// Only one col to parse			for($x = $rowIndex; $x < sizeof($this->fields[$this->currentPage]); $x++) {				if(!$rowIndex == $x && !$this->fields[$this->currentPage][$x][$colIndex]->isPlaceholder())					break;				$this->fields[$this->currentPage][$x][$colIndex]->unsetPlaceholder();				$this->fields[$this->currentPage][$x][$colIndex]->setRowspan(1);			}		}		$sH = tx_mailform_saveState::getInstance();		$sH->setChanged(true);		$this->saveFields();	}	/**	 * splitCellRight($rowIndex, $colIndex)	 *	 * @param Int $rowIndex	 * @param Int $colIndex	 */	public function splitCellRight($rowIndex, $colIndex) {		$this->ensureStatus("be"); // Function only in BE allowed
				if($this->fields[$this->currentPage][$rowIndex][$colIndex]->getRowspan() > 1) {			$cs = $this->fields[$this->currentPage][$rowIndex][$colIndex]->getColspan();			$rs = $this->fields[$this->currentPage][$rowIndex][$colIndex]->getRowspan();			for($x = $rowIndex; $x < ($rowIndex + $rs); $x++) {				for($y = $colIndex; $y < ($colIndex + $cs); $y++) {					$pIl = $this->fields[$this->currentPage][$x][$y]->getPlaceholderIndex();					if($x != $rowIndex) {						$this->fields[$this->currentPage][$x][$y]->setPlaceholderIndex($pIl[0], $y, $this->currentPage);					}					else {						$this->fields[$this->currentPage][$x][$y]->unsetPlaceholder();					}					$this->fields[$this->currentPage][$x][$y]->setColspan(1);					$this->fields[$this->currentPage][$x][$y]->setRowspan($rs);				}			}		} else {			$cs = $this->fields[$this->currentPage][$rowIndex][$colIndex]->getColspan();			for($x = $colIndex; $x < $colIndex + $cs; $x++) {				$this->fields[$this->currentPage][$rowIndex][$x]->unsetPlaceholder();				$this->fields[$this->currentPage][$rowIndex][$x]->setColspan(1);			}		}		$sH = tx_mailform_saveState::getInstance();		$sH->setChanged(true);		$this->saveFields();	}	/**	 * determineMaxColspan()	 *	 * @return Int	 */	public function determineMaxColspan() {
		$this->ensureStatus("be"); // Function only in BE allowed
				$keys = array_keys($this->fields[$this->currentPage]);		return count($this->fields[$this->currentPage][$keys[0]]);	}	/**	 * determineMaxRowspan();	 *	 * @return Int	 */	public function determineMaxRowspan() {
		$this->ensureStatus("be"); // Function only in BE allowed		return count($this->fields[$this->currentPage]);	}		/**
	 * addPage(Char, Integer)
	 *
	 * @param Char $direction
	 * @param Integer $currentPage
	 */	 		public function addPage($direction, $currentPage) {		$this->ensureStatus("be"); // Function only in BE allowed
				if(strtolower($direction) == "r") {			for($x = count($this->fields); $x > $currentPage+1; $x--) {				$feld = $this->fields[$x-1];								foreach($feld as $row) {					foreach($row as $col) {												$col->setPage($x);					}				}								$this->fields[$x] = $feld;			}						unset($this->fields[$currentPage+1]);			$this->fields[$currentPage+1][0][0] = new tx_mailform_field(0,0,$currentPage+1,1,1);			$this->setCurrentPage($currentPage+1);					} else {
						for($x = count($this->fields); $x > $currentPage; $x--) {				$feld = $this->fields[$x-1];				foreach($feld as $row) {					foreach($row as $col) {						$col->setPage($x);					}				}				$this->fields[$x] = $feld;			}			$this->fields[$x][0][0] = new tx_mailform_field(0,0,$x,1,1);		}
				$sH = tx_mailform_saveState::getInstance();		$sH->setChanged(true);		$this->saveFields();	}
	/**
	 * removePage($page)
	 *
	 * @param Integer $page
	 */	public function removePage($page) {
		$this->ensureStatus("be"); // Function only in BE allowed		$this->fields = tx_mailform_funcLib::removeFromArray($this->fields, $page);		$sH = tx_mailform_saveState::getInstance();		$sH->setChanged(true);		$this->saveFields();	}		/**
	 * getPageCount()
	 *
	 * @return unknown
	 */	 		public function getPageCount() {
		$this->ensureStatus("be"); // Function only in BE allowed		return sizeof($this->fields);	}
	
	/**
	 * Function ensures that in ARG given state is running when calling this function
	 *
	 * @param String $allowed_status
	 */
	private function ensureStatus($allowed_status = "BE") {
		if(strtoupper($allowed_status) != TYPO3_MODE)
			throw new Exception("Current Typo3 Mode (".TYPO3_MODE.") is not allowed.");
	}
	
	/**
	 * To String
	 *
	 * @return String
	 */
	public function __toString() {
		return "class.tableFieldHandler";
	}}if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/tt_content_tx_mailform_config/singletons/class.tx_mailform_tablefieldHandler.php'])	{	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/tt_content_tx_mailform_config/singletons/class.tx_mailform_tablefieldHandler.php']);}?>