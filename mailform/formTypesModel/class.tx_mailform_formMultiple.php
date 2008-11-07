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
require_once(t3lib_extMgm::extPath("mailform")."/formTypesModel/class.tx_mailform_formAbstract.php");
require_once(t3lib_extMgm::extPath("mailform")."/formTypesModel/class.tx_mailform_formRequest.php");

/**
* tx_mailform_formMultiple
*
* @to add an additional form type, register the form type in tx_mailform_form.
*
* @author       Sebastian Winterhalder <sw@internetgalerie.ch>
* 
*/
abstract class tx_mailform_formMultiple extends tx_mailform_formRequest {
  	protected $multiplePrefix = "mopt-";
	protected $default_row_elements = 5;
	
  	protected function initMultiple() {
  		if($this->iSvaluesFromDatabase()) {
  			
  			$whereStatement = $this->parse_SQL_WhereStatement($this->configData['database_field_where']);
  			
  			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($this->configData['database_sql_field_value'].",".$this->configData['database_sql_field_display'],
  															$this->configData['database_sql'],
  															$whereStatement
  														);
			
  			$counter = 0;
  			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
  				$this->configData['multiple_option'][$multiplePrefix.$counter]['value'] = $row[$this->configData['database_sql_field_value']];
  				$this->configData['multiple_option'][$multiplePrefix.$counter]['display'] = $row[$this->configData['database_sql_field_display']];
  				$counter++;
			}
  		}
  		
  		if(!is_array($this->configData['multiple_option'])) {
      		$this->configData['multiple_option'] = array();
  		}
  	}
  	
  	/**
  	 * Overwrite Parent containsEmailReceiver();
  	 *
  	 * @return unknown
  	 */
  	public function containsEmailReceiver() {
  		$this->initMultiple();
  		if(!empty($this->configData['use_as_email']) || ($this->configData['use_as_email_choose'] != 'no_recipient' && !empty($this->configData['use_as_email_choose']))) {
  			foreach($this->configData['multiple_option'] as $key => $MOpt) {
				if(is_array($this->postData[$this->configData['type']])) {
					if($this->configData['type'] == 'checkbox' || $this->configData['type'] == 'radio') {
						foreach($this->postData[$this->configData['type']] as $keyX => $valX) {
							if($this->multiplePrefix.$keyX == $key) {

								if(isset($MOpt['email']) || $MOpt['email'] == "on" || $MOpt['email'] == 1) {
									return true;
								}
							}
						}
					} else {
						 if(array_search($key, $this->postData[$this->configData['type']]) !== false && ($MOpt['email'] == "on" || $MOpt['email'] == 1)) {
						 	return true;
						 }
					}
				} else {
					if($key == $this->postData[$this->configData['type']]  && ($MOpt['email'] == "on" || $MOpt['email'] == 1)) {
						return true;
					}
				}
			}
			return false;
  		} else return false;
  	}
  	
  	/**
  	 * Overwrite Parent getContentEmails();
  	 *
  	 * @return unknown
  	 */
  	public function getContentEmails() {
  		$emailArray = array();
  		foreach($this->configData['multiple_option'] as $key => $MOpt) {
			if(is_array($this->postData[$this->configData['type']])) {
				if($this->configData['type'] == 'checkbox' || $this->configData['type'] == 'radio') {
					foreach($this->postData[$this->configData['type']] as $keyX => $valX) {
						if($this->multiplePrefix.$keyX == $key) {
							if(isset($MOpt['email']) || $MOpt['email'] == "on" || $MOpt['email'] == 1) {
								$emailArray[] = $MOpt['value'];
							}
						}
					}
				}
			 if(array_search($key, $this->postData[$this->configData['type']]) !== false && ($MOpt['email'] == "on" || $MOpt['email'] == 1) && t3lib_div::validEmail($MOpt['value']))
			 	$emailArray[] = $MOpt['value'];
			} else {
				if($key == $this->postData[$this->configData['type']]  && ($MOpt['email'] == "on" || $MOpt['email'] == 1) && t3lib_div::validEmail($MOpt['value'])) {
					$emailArray[] = $MOpt['value'];
				}
			}
		}
		return $emailArray;
  	}
 	
	/**
	 *
	 * Get Multiple Value Form HTML
	 *
	 */
	protected function getMultipleHtml() {
		global $LANG, $BACK_PATH;
		require_once(t3lib_extMgm::extPath('mailform').'lib/layout/form/class.tx_mailform_input.php');
		
		$rowArray = $this->getEmailRecipientHtml();
		
		$rowArray[] = $this->makeTitleRow($LANG->getLL('multiple_values'));
		
		// Multiopt TYPE
		require_once(t3lib_extMgm::extPath('mailform').'lib/layout/form/class.tx_mailform_checkbox.php');
		$inputForm = new tx_mailform_checkbox();
		
		// Set Name Procedure
		$name = 'multi_opt_type';
		$inputForm->setName(tx_mailform_funcLib::FORM_POST_PREFIX.'['.$this->configData['uName'].']['.$name.']');
		$inputForm->setChecked($this->configData[$name] == "on" || $selected);
		$inputForm->setComment('makeCheckbox($name, $selected=false)');
		$inputForm->setOnchange('this.form.submit()');
		$inputForm->setOnblur('this.form.submit()');
		$rowArray[] = $this->makeRow($LANG->getLL('database_check_option'), $inputForm->getElementRendered());
		
		if(!empty($this->configData['multi_opt_type'])) {
			$rowArray = array_merge($rowArray, $this->getDataFromDatabase());
		} else {
		
		$MULTIOPT = t3lib_div::_GP('MULTIOPT');
		
		$x = 0;
		if(empty($this->configData['multiple_option']))
			$this->configData['multiple_option'] = array();
		
		foreach($this->configData['multiple_option'] as $MoPt) {
			
			$form = $this->getOptionRow($MoPt['value'], $MoPt['display'], $MoPt['email'], $x);
			if(!isset($MULTIOPT['del'][$this->configData['uName']][$this->multiplePrefix.$x])) {
				$rowArray[] = $this->makeRow($LANG->getLL('multiple_option'), $form);
				unset($this->configData['multiple_option'][$this->configData['uName']][$this->multiplePrefix.$x]);
			}
			$x++;
		}
		
		if(isset($MULTIOPT['add'][$this->configData['uName']])) {
			//$form = '<input type="text" name="'.tx_mailform_funcLib::FORM_POST_PREFIX.'['.$this->configData['uName'].'][multiple_option]['.$this->multiplePrefix.($x+1).']" value="" size="30"/>';
			$form = $this->getOptionRow('', '', '', $x);
			$rowArray[] = $this->makeRow($LANG->getLL('multiple_option'), $form);
		}
		
		$rowArray[] = $this->makeRow('', '<input type="image" name="MULTIOPT[add]['.$this->configData['uName'].']" '.t3lib_iconWorks::skinImg($BACK_PATH, 'gfx/add.gif', $LANG->getLL('form_add_element')).'>');
		}
		
		$rowArray = array_merge($rowArray, $this->getRequestHtml());
		
		return $rowArray;
	}
	
	/**
	 * Returns false if unallowed WHERE Statement
	 * @return Boolean
	 */
	protected function parse_SQL_WhereStatement($statement) {
		// Unallowed Keywords for Query
		$keyWords = "(JOIN|DELETE|UPDATE|SHOW|KEY|FROM|WHERE)";
  		preg_match($keyWords, $this->configData['database_field_where'], $treffer);
			
  		if(count($treffer) > 0)
  			return false;
  				
		preg_match("/([\$]){1,1}([a-zA-Z_-])+([a-zA-Z0-9_-]){0,100}(\['){0,1}([a-zA-Z0-9_-]){0,100}('\]){0,1}/", $statement, $vars);
  		
		$varName = str_replace('$', "", $vars[0]);
		$value = $$varName;
		if(preg_match("/(_GET|_POST)+/", $varName, $trf) > 0) {
			$x = preg_replace("/(_GET|_POST)/", "", $varName);
			$x = str_replace("['", "", $x);
			$x = str_replace("']", "", $x);
			$value = t3lib_div::_GP($x);
		}
		
		$res = str_replace($vars[0], $value, $statement);
		$res = preg_replace("/(\[|\]|\"|\'|\^)/","", $res);
		
		return $res;
	}
	
	/**
	 * getOptionRow($value, $display, $email, $counter)
	 *
	 * @param Mixed $value
	 * @param Mixed $display
	 * @param String $email
	 * @param Integer $counter
	 * @return String
	 */
	private function getOptionRow($value, $display, $email, $counter) {
			global $LANG, $BACK_PATH;
			$x = $counter;
			$inputForm = new tx_mailform_input();
			// Set Name Procedure
			$inputForm->setName(tx_mailform_funcLib::FORM_POST_PREFIX.'['.$this->configData['uName'].'][multiple_option]['.$this->multiplePrefix.$x.'][value]');
			$inputForm->setSize(20);
			$inputForm->setValue($value);
			$inputForm->setComment('multipleFieldOf getMultipleHtml() value');
			$form = " ".$LANG->getLL('multiple_optValue').": ".$inputForm->getElementRendered();
			
			//$form = '<input type="text" name="'.$this->fieldsPrefix.'[multiple_option]['.$this->multiplePrefix.$x.']" value="'.$MoPt.'" size="30"/>';
			
			$inputForm = new tx_mailform_input();
			// Set Name Procedure
			$inputForm->setName(tx_mailform_funcLib::FORM_POST_PREFIX.'['.$this->configData['uName'].'][multiple_option]['.$this->multiplePrefix.$x.'][display]');
			$inputForm->setSize(25);
			$inputForm->setValue(htmlspecialchars($display));
			$inputForm->setComment('multipleFieldOf getMultipleHtml() display');
			$form .= $LANG->getLL('multiple_optDisplay').": ".$inputForm->getElementRendered();
			
			if(isset($this->configData['use_as_email_choose']) && $this->configData['use_as_email_choose'] != 'no_recipient') {
				$inputForm = new tx_mailform_checkbox();
				$inputForm->setType('checkbox');
				$inputForm->setName(tx_mailform_funcLib::FORM_POST_PREFIX.'['.$this->configData['uName'].'][multiple_option]['.$this->multiplePrefix.$x.'][email]');
				if($email == 1 || $email == 'on') {
					$inputForm->setChecked(true);
				}
				$inputForm->setId("mp-select-".$x);
				$inputForm->setComment("Use AS Email");
				$form .= " <label for=\"mp-select-".$x."\">".$LANG->getLL('multiple_optEmail').": </label>".$inputForm->getElementRendered();
			}
			
			$form .= '<input type="image" name="MULTIOPT[del]['.$this->configData['uName'].']['.$this->multiplePrefix.$x.']" '.t3lib_iconWorks::skinImg($BACK_PATH, 'gfx/clearout.gif', $LANG->getLL('form_add_element')).'>';
			return $form;
	}

	/**
	 * getDataFromDatabase()
	 *
	 * @return Array
	 */
	protected function getDataFromDatabase() {
		global $LANG, $BACK_PATH, $CONF;
		
		$rowArray = array();

		$rowArray[] = $this->makeTitleRow($LANG->getLL('database_title'));

		$sql = "SHOW TABLES";
		$allowed = split(",", $CONF['option_database_tables']);
		$res = $GLOBALS['TYPO3_DB']->sql_query($sql);
		$list = array('0' => ' --- Choose --- ');
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_row($res)) {
			$table_name = $row[0];
			$hit = false;
			foreach($allowed as $el) {
				$rep_el = str_replace("*", "", $el);
				if($rep_el != $el) {
					preg_match('('.$rep_el.'[a-zA-Z]{0,100})', $table_name,$treffer);
					if(!empty($treffer)){
						$hit = true;
						break;
					}
				}
			}
			if($hit)
				$list[$row[0]] = $row[0];
		}
		
		$values = "";
		
		$rowArray[] = $this->makeRow(	$LANG->getLL('database_sql'),
										$this->makeSelectbox('database_sql', $list, $this->configData['database_sql'],1,'this.form.submit();') );
		
		if(isset($this->configData['database_sql']) && $this->configData['database_sql'] != '0') {
			$sql = "SHOW COLUMNS FROM ".$this->configData['database_sql'];

			$res = $GLOBALS['TYPO3_DB']->sql_query($sql);
			
			$fields[0] = " --- Choose --- ";
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$fields[$row['Field']] = $row['Field'];
				
				$c++;
			}

			$rowArray[] = $this->makeRow(	$LANG->getLL('database_sql_field_value'),
										$this->makeSelectbox('database_sql_field_value', $fields, $this->configData['database_sql_field_value'],1) );
			$rowArray[] = $this->makeRow(	$LANG->getLL('database_sql_field_display'),
										$this->makeSelectbox('database_sql_field_display', $fields, $this->configData['database_sql_field_display'],1) );
		
			$rowArray[] = $this->makeRow(	$LANG->getLL('database_field_where'),
										$this->makeInputField('database_field_where', $this->configData['database_field_where']) );
		} else {
			$rowArray[] = $this->makeTwoColRow('<font color="#FF0000">'.$LANG->getLL("display_error_not_chosen").'</font>');
		}

		return $rowArray;
	}
	
	/**
	 * iSvaliesFromDatabase()
	 *
	 * @return Boolean
	 */
	protected function iSvaluesFromDatabase() {
		return (!empty($this->configData['database_sql']));
	}
	
	/**
	 * savePost($mailid)
	 * Inherit from tx_mailform_formAbstract
	 *
	 * @param Integer $mailid
	 */
	public function savePost($mailid) {
		if(empty($this->configData['multiple_option']))
			$this->configData['multiple_option'] = array();
		//Post can be Array (Checkbox / Radiobuttons)
		//Or No array (Select)		  		
		if(!empty($this->postData[$this->configData['type']])) {
			if(is_array($this->postData[$this->configData['type']])) {
				$keys = array_keys($this->postData[$this->configData['type']]);
				$arr = array();
				foreach($keys as $key) {      
					$arr[] = $this->configData['multiple_option'][$this->multiplePrefix.$key];
				}
			} else {
				foreach($this->configData['multiple_option'] as $key => $value) {
					if($this->configData['multiple_option'][$key]['value'] == $this->postData[$this->configData['type']]) {
						break;
					}
				}
				$arr[] = $this->configData['multiple_option'][$key];
			}
		} else {
			$arr = array();
		}
		$rawString = t3lib_div::array2xml($arr);
		return $this->dbHan_saveField($mailid, $rawString, '', '', '');
	}
	
	public function getPostValue() {
		$emailRes = array();
		$keys = array_keys($this->configData['multiple_option']);
			$array = $this->configData['multiple_option'];

			
			for($key = 0; $key < sizeof($keys); $key++) {
				$confkey = $this->getUniqueFieldname().'['.$key.']';
				$checked = false;
				
				if(empty($this->postData[$this->configData['type']]))
					$this->postData[$this->configData['type']] = array();
				if(!is_array($this->postData[$this->configData['type']])) {
					foreach($keys as $count => $skey_2) {
						if($array[$skey_2]['value'] == $this->postData[$this->configData['type']]) {
							$result['display'] = $array[$skey_2]['display'];
							$result['value'] = $array[$skey_2]['value'];
							$result['checked'] = true;
							$result['multiple'] = false;
							return $result;
						}
					}
				} 
				else {
					$formType = $this->getFormType();
					switch($formType) {
						case 'checkbox':
							$postKeys = array_keys($this->postData[$this->configData['type']]);
							for($x = 0; $x < sizeof($postKeys); $x++) {
								if($confkey == $this->postData[$this->configData['type']][$postKeys[$x]]) {
									$checked = true;
								}
							}
							$result['multiple'] = true;
						break;
						default:
							// Standard multi value field
							foreach($this->postData[$this->configData['type']] as $value) {
								if($value == $this->configData['multiple_option'][$keys[$key]]['value']) {
									$checked = true;
								}
							}
							$result['multiple'] = false;
						break;
					}
					
					$result['display'] = $array[$keys[$key]]['display'];
					$result['value'] = $array[$keys[$key]]['value'];
					$result['checked'] = $checked;
					
					
					$emailRes[] = $result;
				}
			}
			
			return $emailRes;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/class.tx_mailform_formMultiple.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/class.tx_mailform_formMultiple.php']);
}