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
require_once(t3lib_extMgm::extPath('mailform').'tt_content_tx_mailform_config/model/class.tx_mailform_wizard.php');
require_once(t3lib_extMgm::extPath('mailform').'tt_content_tx_mailform_config/singletons/class.tx_mailform_tablefieldHandler.php');
require_once(t3lib_extMgm::extPath('mailform').'lib/layout/table/class.tx_mailform_table.php');
require_once(t3lib_extMgm::extPath('mailform').'lib/layout/table/class.tx_mailform_tr.php');

/**
 *
 *
 *
 */   
class  tx_mailform_extendedWiz extends tx_mailform_wizard {
	
	private $currentField;
	/**
	 * Initializes the Display
	 * @return	void
	 */
	function __construct()	{
		parent::__construct();
	}
	
	/**
	 * display Functions
	 *
	 */
	protected function displayFunctions() {
		$sH = tx_mailform_saveState::getInstance();

		if(isset($_GET['newPage']) && isset($_GET['currPage'])) {
			$this->addPage($_GET['newPage'], $_GET['currPage']);
		}
		
		if(isset($_GET['formPage']) ) {
			$nextPage = $_GET['formPage'];
			$this->setNextPage($nextPage);
		}

		if(isset($_GET['removePage'])) {
			$this->removePage($_GET['removePage']);
		}
		
		if(isset($_GET['addCol'])) {
			$this->addCol($_GET['addCol']);
			$sH->setChanged(true);
		}
		
		if(isset($_GET['addRow'])) {
			$this->addRow($_GET['addRow']);
			$sH->setChanged(true);
		}
		
		if(isset($_GET['delCol'])) {
			$this->removeCol($_GET['delCol']);
			$sH->setChanged(true);
		}
		
		if(isset($_GET['delRow'])) {
			$this->removeRow($_GET['delRow']);
			$sH->setChanged(true);
		}
		
		if(isset($_GET['mrgCellLrow']) && isset($_GET['mrgCellLcol'])) {
			$this->mergeCellLeft($_GET['mrgCellLrow'], $_GET['mrgCellLcol']);
			$sH->setChanged(true);
		}
		
		if(isset($_GET['mrgCellDcol']) && isset($_GET['mrgCellDrow'])) {
			$this->mergeCellDown($_GET['mrgCellDrow'], $_GET['mrgCellDcol']);
			$sH->setChanged(true);
		}
		
		if(isset($_GET['splitCellDrow']) && isset($_GET['splitCellDcol'])) {
			$this->splitCellDown($_GET['splitCellDrow'], $_GET['splitCellDcol']);
			$sH->setChanged(true);
		}
		
		if(isset($_GET['splitCellLcol']) && isset($_GET['splitCellLrow'])) {
			$this->splitCellRight($_GET['splitCellLrow'], $_GET['splitCellLcol']);
			$sH->setChanged(true);
		}
		
		$this->handlePageWizPost();
	}
	
	/**
	 * get Display
	 *
	 * @return String
	 */
	protected function getDisplay() {
		
		return 
			'<table width="100%">
				<tr>
					<td><input type="hidden" value="'.$this->currentPage.'" name="mailform_currentPage"></td>
				</tr>
				<tr>
					<td>'.$this->getPageBrowser().'</td>
				</tr>
				<tr>
					<td>'.$this->getTable().'</td>
				</tr>
				<tr>
					<td>'.$this->mFieldWizard->getElementRendered().'</td>
				</tr>
			</table>
		';
	}
	
	/**
	 * get Page Browser
	 *
	 * @return String
	 */
	private function getPageBrowser() {
		global $LANG;
		require_once(t3lib_extMgm::extPath('mailform')."lib/class.tx_mailform_urlHandler.php");
		$urlHandler = new tx_mailform_urlHandler();
	
		$getVars = tx_mailform_wizard::getVars();
		$getVars[] = 'formPage';
		
		$table = new tx_mailform_table();
		$table->setWidth("100%");
		$table->setBorder(false);
		$table->setCellspacing(1);
		$table->setCellpadding(0);
		$table->addStyle("border-collapse:collapse; border: 1px solid #B8B8C0; background-color: #E3E3F1;");
		
		$tr = new tx_mailform_tr();
		$td = new tx_mailform_td();
		$td->setRowspan(2);
		
		if($this->currentPage > 0)
			$nextLink = '<a href="'.$urlHandler->getCurrentUrl($getVars).'&formPage='.($this->currentPage-1).'"><img src="../gfx/arrow_left.gif" alt="Previous page" title="Previous page"></a>';
		else 
			$nextLink = '<a href=""><img src="../gfx/arrow_left_denied.gif" alt="No previous page available" title="No previous page available"></a>';
		
		$a = $nextLink.'
					<a href="'.$urlHandler->getCurrentUrl($getVars).'&newPage=l&currPage='.$this->currentPage.'&formPage='.($this->currentPage).'"><img src="../gfx/arrow_left_new.gif"></a>';

		$td->setContent($a);
		$td->addCssClass('fieldWizard-navigation');
		$td->addStyle("vertical-align: middle; text-align: center;");
		$td->setWidth("50px");
		$tr->addTd($td);
		
		$tmtH = tx_mailform_tablefieldHandler::getInstance();
		
		$td = new tx_mailform_td();
		$aLink = "";
		for($x = 0; $x < $tmtH->getPageCount(); $x++) {
			if($x > 0)
				$aLink .= "&nbsp;";
			if($x == $this->currentPage)
				$aLinkText = '<b>-'.($x + 1).'-</b>';
			else
				$aLinkText = $x+1;
			$aLink .= '<a href="'.$urlHandler->getCurrentUrl($getVars).'&formPage='.$x.'">'.$aLinkText.'</a>';
		}
		$td->setContent($aLink);
		$td->addCssClass('fieldWizard-navigation');
		$td->addStyle('text-align: center;');

		$tr->addTd($td);
		
		$tr2 = new tx_mailform_tr();
		$td = new tx_mailform_td();
		
		if($this->currentPage == $tmtH->getPageCount()-1 && $tmtH->getPageCount() != 1)
			$formPage = $this->currentPage-1; else $formPage=$this->currentPage;
		$td->setContent('<a href="'.$urlHandler->getCurrentUrl($getVars).'&formPage='.($this->currentPage).'&editPage='.$this->currentPage.'"><img src="../gfx/edit2.gif"></a>&nbsp;<a href="'.$urlHandler->getCurrentUrl($getVars).'&formPage='.($formPage).'&removePage='.$this->currentPage.'" onclick="return confirm(\''.$LANG->getLL('fWiz_delete_page').'\');"><img src="../gfx/garbage.gif" alt="Delete" title="delete"></a>');
		$td->setAlign("center");
		$tr2->addTd($td);
		
		$td = new tx_mailform_td();
		if($this->currentPage+1 < $tmtH->getPageCount())
			$nextLink = '<a href="'.$urlHandler->getCurrentUrl($getVars).'&formPage='.($this->currentPage+1).'"><img src="../gfx/arrow_right.gif" alt="next page" title="next page" alt="Create new Page after the current" title="Create new Page after the current"></a>';
		else 
			$nextLink = '<a href=""><img src="../gfx/arrow_right_denied.gif" alt="next page" title="next page"></a>';
		
		$a = '<a href="'.$urlHandler->getCurrentUrl($getVars).'&newPage=r&currPage='.$this->currentPage.'&formPage='.($this->currentPage+1).'"><img src="../gfx/arrow_right_new.gif" alt="Create new Page after the current" title="Create new Page after the current"></a>
				'.$nextLink;
		$td->setContent($a);
		$td->addStyle("vertical-align: middle; text-align: center;");
		$td->setWidth("50px");
		$td->setRowspan(2);
		$td->addCssClass('fieldWizard-navigation');
		$tr->addTd($td);
		$table->addRow($tr);
		$table->addRow($tr2);
		
		if(isset($_GET['editPage']) && isset($_GET['formPage']))
			$table->addRow($this->getEditPageWiz());
		
		
		return $table->getElementRendered();
	}
	
	private function getEditPageWiz() {
		global $LANG;
		
		$cfgData = tx_mailform_configData::getInstance();
		$pageConf = $cfgData->getPageConfig();

		$tr = new tx_mailform_tr();
		$td = new tx_mailform_td();
		$td->addStyle('border-top: 1px solid #B8B8C0;');
		$td->setColspan(3);

		$table = new tx_mailform_table();
		$table->setWidth("100%");
		$table->setCellspacing(0);
		$table->setCellpadding(2);
		$trx = new tx_mailform_tr();
		
		$tdx = new tx_mailform_td();
		$tdx->setAlign("right");
			$input = new tx_mailform_input();
			$input->setName('pageconf[singlevalidation]');
			$input->setId('fWiz_check_pagevalidation');
			$input->setType('checkbox');

			$bool = ($pageConf[$this->currentPage]['singlevalidation'] == "on" || $pageConf[$this->currentPage]['singlevalidation'] == 1) ? true : false;
			$input->setChecked($bool);
		$tdx->setContent($input->getElementRendered());
		$trx->addTd($tdx);
		
		$tdx = new tx_mailform_td();
		$tdx->setContent("Page must be validated to continue");
		$trx->addTd($tdx);
		
		$table->addRow($trx);
		
		// Pagename Row
		$trx = new tx_mailform_tr();
		$tdx = new tx_mailform_td();
		$tdx->setAlign('right');
			$input = new tx_mailform_input();
			$input->setName('pageconf[pagetitle]');
			$input->setValue($pageConf[$this->currentPage]['pagetitle']);
		$tdx->setContent($input->getElementRendered());
		$trx->addTd($tdx);
		$tdx = new tx_mailform_td();
		$tdx->setContent($LANG->getLL('fWiz_pagename_title'));
		$trx->addTd($tdx);
		$table->addRow($trx);
		
		// Display Page (Optional)
		$trx = new tx_mailform_tr();
		$tdx = new tx_mailform_td();
		$tdx->setAlign('right');
			$input = new tx_mailform_textarea();
			$input->setName('pageconf[pagecondition]');
			$input->setContent($pageConf[$this->currentPage]['pagecondition']);
			$input->setRows(4);
			$input->setCols(40);
		$tdx->setContent($input->getElementRendered());
		$trx->addTd($tdx);
		$tdx = new tx_mailform_td();
		$tdx->setContent($LANG->getLL('fWiz_page_condition'));
		$trx->addTd($tdx);
		$table->addRow($trx);
		
		// Jump to page if not valid
		$trx = new tx_mailform_tr();
		$tdx = new tx_mailform_td();

		$tdx->setAlign('right');
		$select = new tx_mailform_select();
		$select->setName('pageconf[alternativepage]');
		$select->setSize(1);

		$option = new tx_mailform_option();
		$option->setContent($LANG->getLL('fWiz_alternativepage_nextpage'));
		$option->setValue('nextpage');
		if($pageConf[$this->currentPage]['alternativepage'] == 'nextpage')
			$option->setSelected(true);
		$select->addContent($option);

		$option = new tx_mailform_option();
		$option->setContent($LANG->getLL('fWiz_alternativepage_lastpage'));
		$option->setValue('lastpage');
		if($pageConf[$this->currentPage]['alternativepage'] == 'lastpage')
			$option->setSelected(true);
		$select->addContent($option);
		
		for($x = 1; $x < 5; $x++) {
			$optionX = new tx_mailform_option();
			if(($pageConf[$this->currentPage]['alternativepage'] == $x)) {
				$optionX->setSelected(true);
			} else {
				$optionX->setSelected(false);
			}
			$optionX->setContent($LANG->getLL('fWiz_alternativepage_showpage')." ".$x);
			$optionX->setValue($x);
			$select->addContent($optionX);
		}
			
		$tdx->setContent($select->getElementRendered());
		$trx->addTd($tdx);
		$tdx = new tx_mailform_td();
		$tdx->setContent($LANG->getLL('fWiz_page_alternative'));
		$trx->addTd($tdx);
		$table->addRow($trx);
		
		
		// Jump pre page if not valid
		$trx = new tx_mailform_tr();
		$tdx = new tx_mailform_td();

		$tdx->setAlign('right');
		$select = new tx_mailform_select();
		$select->setName('pageconf[alternativepage_back]');
		$select->setSize(1);

		$option = new tx_mailform_option();
		$option->setContent($LANG->getLL('fWiz_alternativepage_prevpage'));
		$option->setValue('prevpage');
		if($pageConf[$this->currentPage]['alternativepage_back'] == 'prevpage')
			$option->setSelected(true);
		$select->addContent($option);

		$option = new tx_mailform_option();
		$option->setContent($LANG->getLL('fWiz_alternativepage_firstpage'));
		$option->setValue('firstpage');
		if($pageConf[$this->currentPage]['alternativepage_back'] == 'firstpage')
			$option->setSelected(true);
		$select->addContent($option);
		
		for($x = 1; $x < 5; $x++) {
			$optionX = new tx_mailform_option();
			if(($pageConf[$this->currentPage]['alternativepage_back'] == $x)) {
				$optionX->setSelected(true);
			} else {
				$optionX->setSelected(false);
			}
			$optionX->setContent($LANG->getLL('fWiz_alternativepage_showpage')." ".$x);
			$optionX->setValue($x);
			$select->addContent($optionX);
		}
			
		$tdx->setContent($select->getElementRendered());
		$trx->addTd($tdx);
		$tdx = new tx_mailform_td();
		$tdx->setContent($LANG->getLL('fWiz_page_alternative_back'));
		$trx->addTd($tdx);
		$table->addRow($trx);
		
		
		// Save Button row
		$trx = new tx_mailform_tr();
		$tdx = new tx_mailform_td();
		$tdx->setAlign('right');
		$input = new tx_mailform_input();
		$input->setName('pageconf[submit]');
		$input->setValue($LANG->getLL('fWiz_saveButton'));
		$input->setType('submit');
		$tdx->setContent($input->getElementRendered());
		$tdx->setColspan(2);
		$tdx->addStyle('border-top: 1px solid #B8B8C0;');
		$trx->addTd($tdx);
		$table->addRow($trx);
		
		$td->setContent($table->getElementRendered());
		$tr->addTd($td);
		return $tr;
	}
	
	private function handlePageWizPost() {
		$P = t3lib_div::_GP('pageconf');

		$configData = tx_mailform_configData::getInstance();
		$pageConfig = $configData->getPageConfig();
		
		if(!isset($P['singlevalidation']) && isset($P['submit'])) {
			$pageConfig[$this->currentPage]['singlevalidation'] = "";
		} else {
			$pageConfig[$this->currentPage]['singlevalidation'] = "on";
		}
		
		if(isset($P['pagetitle']))
			$pageConfig[$this->currentPage]['pagetitle'] = $P['pagetitle'];
		
		if(isset($P['pagecondition'])) {
			$pageConfig[$this->currentPage]['pagecondition'] = $P['pagecondition'];
		}
		
		if(isset($P['alternativepage'])) {
			$pageConfig[$this->currentPage]['alternativepage'] = $P['alternativepage'];
		}

		if(isset($P['alternativepage_back'])) {
			$pageConfig[$this->currentPage]['alternativepage_back'] = $P['alternativepage_back'];
		}
		
		if(isset($P['submit'])) {
			$ss = tx_mailform_saveState::getInstance();
			$ss->setChanged(true);
		}

		$configData->setPageconfData($pageConfig);
	}
	
	private function getTable() {
		global $LANG;
		$tmth = tx_mailform_tablefieldHandler::getInstance();
		$this->fields = $tmth->getFields();
		
		require_once(t3lib_extMgm::extPath('mailform')."lib/class.tx_mailform_urlHandler.php");
		$urlHandler = new tx_mailform_urlHandler();
		$getVars = tx_mailform_wizard::getVars();
		$table = new tx_mailform_table();
		$table->setWidth("100%");
		$table->setBorder(false);
		$table->setCellpadding(0);
		$table->setCellspacing(0);
		$table->addStyle("border-collapse:collapse;");
		
		// Table Row
		$row = new tx_mailform_tr();
		$cell = new tx_mailform_td();
		$cell->addCssClass('wiz_Table_Navi_Outer');
		$cell->addCSSClass('wiz_Table_Navi_TOPLEFT');
		$cell->setAlign('center');
		$cell->setContent('<a style="color: #e80303;" href="'.$urlHandler->getCurrentUrl($getVars,true).'&amp;addCol=-1"><img src="../gfx/insert_col_top.gif" alt="'.$LANG->getLL('fWiz_insert_col').'" title="'.$LANG->getLL('fWiz_insert_col').'" border="0"></a>');
		$row->addTd($cell);

		for($x = 0; $x < $tmth->determineMaxColspan(); $x++) {
			$cell = new tx_mailform_td();
			$cell->addCssClass('wiz_Table_Navi_Outer');
			$cell->addCSSClass('wiz_Table_Navi_TOP_Repeat');
			$cell->setAlign('center');
			$urlHandler = new tx_mailform_urlHandler();
			$cell->setContent('<a style="color:#0c9d9f;" href="'.$urlHandler->getCurrentUrl($getVars,true).'&amp;delCol='.$x.'"><img src="../gfx/remove_col_top.gif" alt="'.$LANG->getLL('fWiz_delete_col').'" title="'.$LANG->getLL('fWiz_delete_col').'" border="0"></a>
								<a style="color: #e80303;" href="'.$urlHandler->getCurrentUrl($getVars,true).'&amp;addCol='.$x.'"><img src="../gfx/insert_col_top.gif" alt="'.$LANG->getLL('fWiz_insert_col').'" title="'.$LANG->getLL('fWiz_insert_col').'" border="0"></a>
								');
			$row->addTd($cell);
		}
		
		$cell = new tx_mailform_td();
		$cell->addCssClass('wiz_Table_Navi_Outer');
		$cell->addCSSClass('wiz_Table_Navi_TOPRIGHT');
		$cell->setContent('<a href="'.$urlHandler->getCurrentUrl($getVars,true).'&amp;addRow=-1"><img src="../gfx/insert_row.gif" alt="'.$LANG->getLL('fWiz_insert_row').'" title="'.$LANG->getLL('fWiz_insert_row').'" border="0"></a>');
		$row->addTd($cell);
		$table->addRow($row);

		foreach($this->fields[$this->currentPage] as $rowKey => $rows) {
			$row = new tx_mailform_tr();
			
			$cell = new tx_mailform_td();
			$cell->addCssClass('wiz_Table_Navi_Outer');
			$cell->addCssClass('wiz_Table_Navi_LEFT_Repeat');
			
			//$cell->addStyle('border: 1px solid #777; background-color: #f3f3f4; text-align:center; vertical-align:middle;');
			$cell->setContent('<a href="'.$urlHandler->getCurrentUrl($getVars,true).'&amp;addRow='.$rowKey.'"><img src="../gfx/insert_row_left.gif" alt="'.$LANG->getLL('fWiz_insert_row').'" title="'.$LANG->getLL('fWiz_insert_row').'" border="0"></a>
									<a href="'.$urlHandler->getCurrentUrl($getVars,true).'&amp;delRow='.$rowKey.'"><img src="../gfx/remove_row_left.gif" alt="'.$LANG->getLL('fWiz_delete_row').'" title="'.$LANG->getLL('fWiz_delete_row').'" border="0"></a>
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
					$cell->setValign(tx_mailform_attr_valign::VALIGN_TOP);
					$cell->addCssClass('wiz_Table_Cell');
					$cell->addStyle('border: 1px solid #444;');
					$row->addTd($cell);
				}
			}
			
			$cell = new tx_mailform_td();
			$cell->addCssClass('wiz_Table_Navi_Outer');
			$cell->addCssClass('wiz_Table_Navi_RIGHT_Repeat');
			//$cell->addStyle('border: 1px solid #777; background-color: #bdc0d8; text-align:center; vertical-align:middle;');
			$cell->setContent('<a href="'.$urlHandler->getCurrentUrl($getVars,true).'&amp;addRow='.$rowKey.'"><img src="../gfx/insert_row.gif" alt="'.$LANG->getLL('fWiz_insert_row').'" title="'.$LANG->getLL('fWiz_insert_row').'" border="0"></a>
									<a href="'.$urlHandler->getCurrentUrl($getVars,true).'&amp;delRow='.$rowKey.'"><img src="../gfx/remove_row.gif" alt="'.$LANG->getLL('fWiz_delete_row').'" title="'.$LANG->getLL('fWiz_delete_row').'" border="0"></a>
			');
			$cell->setRowspan(1);
			$cell->setWidth(16);
			$row->addTd($cell);
			
			$table->addRow($row);
		}
		
		// Table Row
		$row = new tx_mailform_tr();
		$cell = new tx_mailform_td();
		$cell->addCssClass('wiz_Table_Navi_Outer');
		$cell->addCssClass('wiz_Table_Navi_BOTTOMLEFT');
		$cell->setContent('<a style="color: #e80303;" href="'.$urlHandler->getCurrentUrl($getVars,true).'&amp;addCol=-1"><img src="../gfx/insert_col.gif" alt="'.$LANG->getLL('fWiz_insert_col').'" title="'.$LANG->getLL('fWiz_insert_col').'" border="0"></a>
								');
		$row->addTd($cell);
		
		for($x = 0; $x < $tmth->determineMaxColspan(); $x++) {
			$cell = new tx_mailform_td();
			$cell->addCssClass('wiz_Table_Navi_Outer');
			$cell->addCSSClass('wiz_Table_Navi_BOTTOM_Repeat');
			$cell->setAlign('center');
			$urlHandler = new tx_mailform_urlHandler();
			$cell->setContent('<a style="color:#0c9d9f;" href="'.$urlHandler->getCurrentUrl($getVars,true).'&amp;delCol='.$x.'"><img src="../gfx/remove_col.gif" alt="'.$LANG->getLL('fWiz_delete_col').'" title="'.$LANG->getLL('fWiz_delete_col').'" border="0"></a>
									<a style="color: #e80303;" href="'.$urlHandler->getCurrentUrl($getVars,true).'&amp;addCol='.$x.'"><img src="../gfx/insert_col.gif" alt="'.$LANG->getLL('fWiz_insert_col').'" title="'.$LANG->getLL('fWiz_insert_col').'" border="0"></a>
				');
			$row->addTd($cell);
		}
		
		$cell = new tx_mailform_td();
		$cell->addCssClass('wiz_Table_Navi_Outer');
		$cell->addCssClass('wiz_Table_Navi_BOTTOMRIGHT');
		$cell->setContent('');
		$row->addTd($cell);
		$table->addRow($row);
		
		return $table->getElementRendered();
	}
	
	private function addCol($colIndex) {
		$tmtH = tx_mailform_tablefieldHandler::getInstance();
		$tmtH->addCol($colIndex);
	}
	
	private function addRow($rowIndex) {
		$tmtH = tx_mailform_tablefieldHandler::getInstance();
		$tmtH->addRow($rowIndex);
	}
	
	private function removeCol($colIndex) {
		$tmtH = tx_mailform_tablefieldHandler::getInstance();
		$tmtH->removeCol($colIndex);
	}
	
	private function removeRow($rowIndex) {
		$tmtH = tx_mailform_tablefieldHandler::getInstance();
		$tmtH->removeRow($rowIndex);
	}
		
	private function mergeCellDown($rowIndex, $colIndex) {
		$tmtH = tx_mailform_tablefieldHandler::getInstance();
		$tmtH->mergeCellDown($rowIndex, $colIndex);
	}
	
	private function mergeCellLeft($rowIndex, $colIndex) {
		$tmtH = tx_mailform_tablefieldHandler::getInstance();
		$tmtH->mergeCellLeft($rowIndex, $colIndex);
	}
	
	private function splitCellDown($rowIndex, $colIndex) {
		$tmtH = tx_mailform_tablefieldHandler::getInstance();
		$tmtH->splitCellDown($rowIndex, $colIndex);
	}
	
	private function splitCellRight($rowIndex, $colIndex) {
		$tmtH = tx_mailform_tablefieldHandler::getInstance();
		$tmtH->splitCellRight($rowIndex, $colIndex);
	}
	
	private function addPage($direction, $current) {
		if(strtolower($direction) != "l" && strtolower($direction) != "r")
			throw new Exception('Wrong argument passed. Only R or L allowed');
		$tmtH = tx_mailform_tablefieldHandler::getInstance();
		$tmtH->addPage($direction, $current);
		
		if($direction == "r") {
			$tmtH->setCurrentPage($page);
			$this->currentPage = $page;
		} else {
			$tmtH->setCurrentPage($page);
			$this->currentPage = $page;
		}
	}
	
	private function setNextPage($page) {
		$tmtH = tx_mailform_tablefieldHandler::getInstance();
		$tmtH->setCurrentPage($page);
		$this->currentPage = $page;
	}
	
	private function removePage($removePage) {
		$tmtH = tx_mailform_tablefieldHandler::getInstance();
		if($tmtH->getPageCount() > 1) {
			$tmtH->removePage($removePage);
		}
	}
	
}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/tt_content_tx_mailform_config/model/class.tx_mailform_extendedWiz.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/tt_content_tx_mailform_config/model/class.tx_mailform_extendedWiz.php']);
}
?>