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
require_once(t3lib_extMgm::extPath("mailform")."/formTypesModel/models/class.tx_mailform_formSelect.php");

/**
 * tx_mailform_formSelect
 *
 * @author	Sebastian Winterhalder <typo3@internetgalerie.ch>
 *
 */
class tx_mailform_formStaticcountry extends tx_mailform_formSelect {

	protected $requireBox = true; // Display require box
	protected $form_select_size = 1;
	protected $be_typeImage = '../gfx/type/select.gif';

	/**
	 * Initialization
	 *
	 */
	protected function fieldInit() {
		$this->initMultiple();
		$this->hasInitialized = true;
	}

	/**
	 * load Child Data
	 *
	 */
	protected function loadChildData() {
		if(isset($this->configData['sit_display_language'])) {
			if($this->configData['sit_display_language'] == 'auto') {
				$this->configData['sit_display_language'] = $GLOBALS['LANG']->lang;
			}
			
			$exc_sql = '';
			if(!empty($this->configData['static_exclude_csv'])) {
				$exclude = split(",", $this->configData['static_exclude_csv']);
				for($x = 0; $x < count($exclude); $x++) {
					if($x > 0)
						$exc_sql .= " AND"; 
					$exc_sql .= " NOT cn_iso_3 = '".$exclude[$x]."'";   	 
				}
			}
			
			
			$inc_sql = '';
			if(!empty($this->configData['static_include_csv'])) {
				$include = split(",",$this->configData['static_include_csv']);
				for($x = 0; $x < count($include); $x++) {
					if($x > 0)
						$inc_sql .= " OR"; 
					$inc_sql .= " cn_iso_3 = '".$include[$x]."'";   	 
				}
			}
			
			if($inc_sql != '' || $exc_sql != '')
				$add_sql = " WHERE";
			if($inc_sql != '' ) {
				$add_sql .= " (".$inc_sql.")";
			}
			if($inc_sql != '' && $exc_sql != '')
				$add_sql .= " AND";
			if($exc_sql != '') {
				$add_sql .= ' ('.$exc_sql.')';
			}
				
			$sql = "SELECT cn_short_".$this->configData['sit_display_language'].",cn_iso_3 FROM static_countries
					$add_sql	
					ORDER BY cn_short_".$this->configData['sit_display_language']." ASC
				";
			$res = $GLOBALS['TYPO3_DB']->sql_query($sql);
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_row($res)) {
				if(empty($this->configData['choose_display_shortcuts'])) {
					$resArr = array('value' => $row[1], 'display' => $row[0]);
				} else $resArr = array('value' => $row[0], 'display' => $row[0]);
				
				$this->configData['multiple_option'][$row[1]] = $resArr;
			}
		}
	//	t3lib_div::debug($this->configData);
	}
	
	/**
	 * Render the HTML (Inherited from formAbstract)
	 *
	 *@return String
	 */
	protected function renderHtml() {
		global $LANG;
		$array = $this->getSelectOptions();
		
		$array[] = $this->makeTitleRow($LANG->getLL('form_options'));
		$array[] = $this->makeRow('static_exclude_csv', $this->makeInputField('static_exclude_csv', $this->configData['static_exclude_csv']));
		$array[] = $this->makeRow('static_include_csv', $this->makeInputField('static_include_csv', $this->configData['static_include_csv']));
		
		if(!t3lib_extMgm::isLoaded('static_info_tables')) {
			$array[] = $this->makeTwoColRow('<font color="#FF0000">Extension \'info_static_tables\' not loaded.</font>');
		}
		
		$sql = "SHOW FIELDS FROM `static_countries`";
		$res = $GLOBALS['TYPO3_DB']->sql_query($sql);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			if(preg_match('(^cn_short_[a-z]{2,2}$)', $row['Field'])) {
				$sc = str_replace('cn_short_', '', $row['Field']);
				$arr[] = str_replace('cn_short_', '', $row['Field']);
			}
		}
		
		$val['auto'] = $LANG->getLL('static_info_tables_lngauto');
		$val['local'] = $LANG->getLL('static_info_tables_lnglocal');
		$sql = "SELECT * FROM static_languages WHERE";
		for($x = 0; $x < count($arr); $x++) {
			if($x > 0) $sql .= " OR";
			$sql .= "  lg_typo3 = '".strtolower($arr[$x])."' OR lg_iso_2 = '".strtolower($arr[$x])."'";
		}
		
		$res = $GLOBALS['TYPO3_DB']->sql_query($sql);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			if($row['lg_typo3'] == "") {
				$val["en"] = "English";
			} else {
				$val[strtolower($row['lg_typo3'])] = $row['lg_name_local'];
			}
		}
		
		$array[] = $this->makeRow($LANG->getLL('choose_display_language'), $this->makeSelectbox('sit_display_language', $val, $this->configData['sit_display_language']));
		$array[] = $this->makeRow($LANG->getLL('choose_display_shortcuts'), $this->makeCheckbox('sit_display_shortcut', $this->configData['sit_display_shortcut']));
		return $array;
	}

	/**
	* Get Select Options (HTML)
	*
	*@return String
	*/
	private function getSelectOptions() {
		global $LANG;

		$multiple = $this->makeCheckbox('forms_select_multiple', $this->forms_select_multiple)." ".$LANG->getLL('forms_select_allowMultiple');

		$array = array();
		$array = array_merge($array, $this->row_preRequiredSpecValue(true));
		$array[] = $this->makeRow($LANG->getLL('forms_select_size'), $this->makeInputField('forms_select_size', $this->forms_select_size).$multiple);
		$required = $this->makeCheckbox('forms_select_multiple', $this->forms_select_multiple)." ".$LANG->getLL('forms_select_allowMultiple');
		$array[]	= $this->makeRow($LANG->getLL('form_standard_value'), $this->makeInputField('form_standard_value', $this->configData['form_standard_value']));
		
		return $array;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_formSelect.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_formSelect.php']);
}
?>