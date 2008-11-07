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

/**
* mailform module tt_content_tx_mailform_forms
*
* @author       Sebastian Winterhalder <sw@internetgalerie.ch>
*/
class tx_mailform_formContelement extends tx_mailform_formAbstract {

	private $form_contelement_id = 0;
	public $form_contelement_idArr = array();
	public $form_contelement_idArr_list = array();
	protected $requireBox = false;
	protected $be_typeImage = '../gfx/type/standard.gif';

	/**
	 * fieldInit()
	 *
	 */
	protected function fieldInit() {
		$this->hasInitialized = true;
	}
	
	/**
	 * renderFrontend()
	 *
	 * @return String
	 */
	protected function renderFrontend() {
		// Set Post value into field
			$elM = $this->splitElements( $this->configData['form_contelement_idArr']);
		$elId = array();
		foreach($elM as $eTmp) {
			$elId[] = str_replace("tt_content_", "", $eTmp);
		}
		$tt_content_conf = array('tables' => 'tt_content','source' => implode(",", $elId),'dontCheckPid' => 1);
	
		
		$cObj = t3lib_div::makeInstance('ux_tslib_cObj');
		$content = $cObj->RECORDS($tt_content_conf); 
	
		return $this->getWithTemplate($this->configData['label'], $content, $this->isFormRequired(), -1);
	}

	/**
	 * row_preMakeBrowserContent()
	 *
	 * @return String
	 */
	protected function row_preMakeBrowseContent() {
		global $LANG;
	
		$formName = tx_mailform_funcLib::FORM_POST_PREFIX.'['.$this->configData['uName'].'][form_contelement_idArr]';
		$str = '';
		$arrVal = $this->splitElements($this->configData['form_contelement_idArr']);
		$elArr = array();
		foreach($arrVal as $el) {
			$elArr[] = str_replace("tt_content_", "", $el);
		}
		
		$oldValues = $this->configData['form_contelement_idArr'];
		if(sizeof($elArr) > 0) {
			$sql = "SELECT header,uid FROM tt_content WHERE uid = ".implode(" OR uid = ", $elArr);
	
			$res = $GLOBALS['TYPO3_DB']->sql_query($sql);
			$rows = array();
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$rows[] = $row;
			}
		
			// Benutzerreihenfolge der Datensätze erstellen
			while(sizeof($rows) > 0) {
				foreach($elArr as $uid) {
					foreach($rows as $key => $row) {
						if($row['uid'] == $uid) {
							$opt = new tx_mailform_option();
							$opt->setValue('tt_content_'.$row['uid']);
							if($row['header'] != "")
								$opt->setContent($row['header']);
							else {
								$opt->setContent("[ID ".$row['uid'].": ".$LANG->getLL('no_title')."]");
							}
							$str .= $opt->getElementRendered();
							unset($rows[$key]);
						}
					}
				}
			}
		}
	
		$cont = '
			<table border="0" cellpadding="0" cellspacing="0" width="1">
			<tr>
			<td valign="top"><select size="3" class="formField5" multiple="multiple" name="'.$formName.'_list"  style="width:250px;">'.$str.'</select><br /><br /></td>
			<td valign="top"><a href="#" onclick="setFormValueManipulate(\''.$formName.'\',\'Remove\'); return false"><img src="http://'.t3lib_div::getIndpEnv('HTTP_HOST').'/typo3/sysext/t3skin/icons/gfx/group_clear.gif" width="14" height="14" border="0"  alt="Ausgewähltes Objekt löschen" title="Ausgewähltes Objekt löschen" /></a></td>
			<td valign="top"><a href="#" onclick="setFormValueOpenBrowser(\'db\',\''.$formName.'|||tt_content|\', \'http://'.t3lib_div::getIndpEnv('HTTP_HOST').'/typo3/\'); return false;"><img src="http://'.t3lib_div::getIndpEnv('HTTP_HOST').'/typo3/sysext/t3skin/icons/gfx/insert3.gif" width="15" height="15" border="0"  alt="Durch Datensätze browsen" title="Durch Datensätze browsen" /></a></td>
			<td><input type="hidden" name="'.$formName.'" value="'.$oldValues.'" /></td>
			</tr>
			</table>
		';
		return $this->makeRow(	$LANG->getLL('form_contelement_id'), $cont );
	}
  
	/**
	 * renderHtml()
	 *
	 * @return Array
	 */
	protected function renderHtml() {
		$array = $this->getAdditionalFields();
		return $array;
	}

	/**
	 * getAdditionalFields()
	 *
	 * @return Array
	 */
	private function getAdditionalFields() {
		global $LANG;
		$array = array();
		$array[] = $this->makeTitleRow($LANG->getLL('form_options'));
		$array[] = $this->row_preMakeBrowseContent();
		return $array;
	}

	/**
	 * enteredRequired()
	 * Post validation
	 * Inherit from tx_mailform_formAbstract
	 */
	public function enteredRequired() {
		if($this->isFormRequired()) {
			if($this->postVarSet()) {
				return (strlen($this->postData[$this->configData['type']]) > 0);
			} else return false;
		} else return true;
	}

	/**
	 * validateField()
	 * Inherit from tx_mailform_formAbstract
	 *
	 */
	public function validateField() {
		// Make sure the field is not twice validated
		if($this->alreadyValidated)
			return true;
		parent::validateField();
		
		$this->appendFormDataValid(TRUE, "Valid Field Content");
	}

	/**
	 * getFieldValue
	 *
	 * @return String
	 */
	public function getFieldValue() {
		return $this->configData['input_field_value']."EMAIL VALUE?";
	}
	
	/**
	 * getEmailValue
	 *
	 * @param Mixed $rawText
	 * @return String
	 */
	public function getEmailValue($rawText=true) {
		// At this version do not allow in EMAIL t3 content
		return '';
		
		if($rawText == false) {
			$elM = $this->splitElements( $this->configData['form_contelement_idArr']);
			foreach($elM as $eTmp) {
				$elId[] = str_replace("tt_content_", "", $eTmp);
			}
			$tt_content_conf = array('tables' => 'tt_content','source' => implode(",", $elId),'dontCheckPid' => 1);
	
			$content = $this->cObj->RECORDS($tt_content_conf); 
			return $content;
		} else {
			$GLOBALS['LANG']->includeLLFile(t3lib_extMgm::extPath('mailform')."pi1/locallang.xml");
			return $GLOBALS['LANG']->getLL('cannot_display_data');
		}
	}

	/**
	 * validFieldPost()
	 *	Use this function to validate if the post is ready to send!
	 * 
	 * @return Boolean
	 */
	public function validFieldPost() {
		return true;
	}

	/**
	 * savePost
	 * Inherited from formAbstract
	 *
	 * @param Integer $mailid
	 */
	public function savePost($mailid) {
		return $this->dbHan_saveField($mailid, '', $this->postData['text'], '', '');
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_formContelement.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_formContelement.php']);
}
?>