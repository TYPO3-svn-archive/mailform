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
require_once(t3lib_extMgm::extPath('mailform').'tt_content_tx_mailform_config/model/class.tx_mailform_field.php');
require_once(t3lib_extMgm::extPath('mailform').'tt_content_tx_mailform_config/singletons/class.tx_mailform_tablefieldHandler.php');

/**
 * tx_mailform_wizard
 * 
 * @author Sebastian Winterhalder <sw@internetgalerie.ch>
 *
 */
abstract class tx_mailform_wizard {
	
	protected $P;
	protected $currentPage = 0;
	protected $formElements = array();
	protected $configData = array();
	protected $fields = array(array());
	protected $mFieldWizard;
	public static $GET_vars = array('addCol',
																		'addRow',
																		'delRow',
																		'delCol',
																		'mrgCellDrow',
																		'mrgCellDcol',
																		'mrgCellLrow',
																		'mrgCellLcol',
																		'delReferences',
																		'sFieldIndex',
																		'currPage',
																		'newPage',
																		'removePage',
																		'editPage',
																		);
	
	/**
	 * Abstract Constructor
	 * Class parent initializator
	 */
	public function __construct() {
		$this->P = t3lib_div::_GP('P');
		
		require_once(t3lib_extMgm::extPath('mailform').'tt_content_tx_mailform_config/model/class.tx_mailform_fieldWiz.php');
		$this->mFieldWizard = new tx_mailform_fieldWiz($this);
		
		$tmtH = tx_mailform_tablefieldHandler::getInstance();
		$tmtH->setCurrentPage($this->currentPage);
	}
	
	public function init() {
		$this->displayFunctions();
		$tmth = tx_mailform_tablefieldHandler::getInstance();
		$this->fields = $tmth->getFields();
		
	}
	
	protected abstract function displayFunctions();
	
	public function getElementRendered() {
		require_once(t3lib_extMgm::extPath('mailform').'lib/layout/table/class.tx_mailform_table.php');
		$table = new tx_mailform_table();
		$table->setCellpadding(0);
		$table->setCellspacing(0);
		$table->setBorder(false);
		$table->setSummary('OuterContainer');
		$table->setWidth("100%");
		
		$row = new tx_mailform_tr();
		$col = new tx_mailform_td();
		$col->setContent($this->getDisplay());
		$row->addTd($col);
		$table->addRow($row);
		return $table->getElementRendered();
	}
	
	protected abstract function getDisplay();
	
	public static function getVars() {
		require_once(t3lib_extMgm::extPath('mailform').'tt_content_tx_mailform_config/model/class.tx_mailform_fieldWiz.php');
		$arr = array_merge(tx_mailform_wizard::$GET_vars, tx_mailform_fieldWiz::$GET_fieldVars);
		$arr = array_merge($arr, tx_mailform_field::$fieldVars);
		return $arr;
	}
	
	/**
	 * Get a FORM element with ufid
	 *
	 * @param String $ufid
	 * @return Object
	 */
	public function getForm($ufid) {
		if(strlen($ufid) == 0) {
			throw new Exception("tx_mailform_wizard: Wrong argument given.");
		}
		$tmfH = tx_mailform_formHandler::getInstance();
		$forms = $tmfH->getForms();
		foreach($forms as $field) {
			if($field->getForm()->getUFID() == $ufid)
				return $field;
		}
		die('Field not found in configuration');
	}
	
	public function getConfigFromDatabase($temp = false) {
		$temp = $temp ? '1' : '0';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_mailform_pluginConf_fields', 'pUid='.$this->P['uid'].' AND tmp = '.$temp.' ORDER BY field_key');
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$configData[$row['field_page']][$row['field_index']][$row['field_attr']] = $row['field_value'];
			$configData[$row['field_page']][$row['field_index']]['uName'] = $row['field_key'];
		}
		return $configData;
	}
	
	protected function saveFormsToDatabase($configData, $temp = true) {
		// deprecated
	}

	/**
	 * getConfigData
	 * This is the Method used when e.g. the form is saved to XML and Database
	 *
	 * @return unknown
	 */
	public function getConfigData() {
		$res = array();
		foreach($this->fields[$this->currentPage] as $fieldRow) {
			foreach($fieldRow as $field) {
				$res[] = $field->getConfigData();
			}
		}
		return $res;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/tt_content_tx_mailform_config/model/display/class.tx_mailform_wizard.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/tt_content_tx_mailform_config/model/display/class.tx_mailform_wizard.php']);
}
?>