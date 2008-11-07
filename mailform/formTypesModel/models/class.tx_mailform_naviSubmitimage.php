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
require_once(t3lib_extMgm::extPath("mailform")."/formTypesModel/class.tx_mailform_naviSubmitAbstract.php");

/**
* mailform module tt_content_tx_mailform_forms
*
* @author       Sebastian Winterhalder <sw@internetgalerie.ch>
*/
class tx_mailform_naviSubmitimage extends tx_mailform_naviSubmitAbstract {

	// Frontend field requires post when post is sent
	// Overwrite from mailform_formAbstract
	protected $postRequired = false;
	protected $requireBox = false;
	protected $be_typeImage = '../gfx/type/submit.gif';

	/**
	 * renderFrontend()
	 *
	 * @return String
	 */
	protected function renderFrontend() {
		if(t3lib_extMgm::isLoaded('dam') && (!$this->configData['form_button_useurl'])) {
			$damElement = $this->getDAM_DB_rows();
			$imgPath = $damElement[0]['file_path'].$damElement[0]['file_name'];
		} else {
			$imgPath = $this->configData['form_button_imageurl'];
		}
		
		$field = '<input id="'.$this->getUniqueIDName("input").'" class="tx_mailform_submit" type="image" name="'.$this->getVarPrefix().'[submit][0]" src="'.$imgPath.'" alt="Image Button" value="'.$this->configData['input_field_value'].'" />
					<input type="hidden" name="'.$this->getVarPrefix().'[submit][1]" value="submit" />
					';
		return $this->getWithTemplate($this->configData['label'], $field);
	}

	/**
	 * getDAM_DB_rows()
	 *
	 * @return Array
	 */
	protected function getDAM_DB_rows() {
		$arrVal = $this->splitElements($this->configData['form_contelement_idArr']);
		$elArr = array();
		foreach($arrVal as $el) {
			$elArr[] = str_replace("tx_dam_", "", $el);
		}

		$rows = array();
		if(sizeof($elArr) > 0) {
			$sql = "SELECT file_path,uid,file_name,hpixels,vpixels FROM tx_dam WHERE uid = ".implode(" OR uid = ", $elArr);
			$res = $GLOBALS['TYPO3_DB']->sql_query($sql);
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$rows[] = $row;
			}
		}
		return $rows;
	}

	/**
	 * renderHtml()
	 *
	 * @return Array
	 */
	protected function renderHtml() {
		global $LANG;
		$array = array();
		$array[] = $this->makeTitleRow($LANG->getLL('form_options'));
		//$array[] = $this->row_preFormButtonValue();
		
		//$array[] = 
		if(t3lib_extMgm::isLoaded('dam')) {
			$array[] = $this->makeRow( $LANG->getLL('form_button_useurl'), $this->makeCheckbox('form_button_useurl'));
		}
		$array[] = $this->row_getURL();
		$array[] = $this->row_preExcludeFromStats();
		//$array[] = $this->displayOptions();
		return $array;
	}

	/**
	 * row_getURL();
	 *
	 * @return Object
	 */
	protected function row_getURL() {
		global $LANG;
		if(t3lib_extMgm::isLoaded('dam') && (empty($this->configData['form_button_useurl']))) {
			return $this->row_preMakeBrowseContentDAM();
		} else {
			return $this->makeRow( $LANG->getLL('form_button_imageurl'),
									$this->makeInputField('form_button_imageurl', $this->configData['form_button_imageurl']));
		}
	}

	/**
	 * row_preMakeBrowseContentDAM()
	 *
	 * @return Object
	 */
	protected function row_preMakeBrowseContentDAM() {
		global $LANG;

		$formName = tx_mailform_funcLib::FORM_POST_PREFIX.'['.$this->configData['uName'].'][form_contelement_idArr]';
		$str = '';

		$arrVal = $this->splitElements($this->configData['form_contelement_idArr']);
		$elArr = array();
		foreach($arrVal as $el) {
			$elArr[] = str_replace("tx_dam_", "", $el);
		}

		$oldValues = $this->configData['form_contelement_idArr'];
		if(sizeof($elArr) > 0) {
			$sql = "SELECT uid,file_name FROM tx_dam WHERE uid = ".implode(" OR uid = ", $elArr);

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
							$opt->setValue('tx_dam_'.$row['uid']);
							if($row['file_name'] != "")
								$opt->setContent($row['file_name']);
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
			<td valign="top"><select size="3" class="formField5" multiple="single" name="'.$formName.'_list"  style="width:250px;">'.$str.'</select><br /><br /></td>
			<td valign="top"><a href="#" onclick="setFormValueManipulate(\''.$formName.'\',\'Remove\'); return false"><img src="http://'.t3lib_div::getIndpEnv('HTTP_HOST').'/typo3/sysext/t3skin/icons/gfx/group_clear.gif" width="14" height="14" border="0"  alt="Ausgewähltes Objekt löschen" title="Ausgewähltes Objekt löschen" /></a></td>
			<td valign="top"><a href="#" onclick="setFormValueOpenBrowser(\'db\',\''.$formName.'|||tx_dam|gif,jpg,jpeg,tif,bmp,pcx,tga,png||\', \'http://'.t3lib_div::getIndpEnv('HTTP_HOST').'/typo3/\'); return false;"><img src="http://'.t3lib_div::getIndpEnv('HTTP_HOST').'/typo3/sysext/t3skin/icons/gfx/insert3.gif" width="15" height="15" border="0"  alt="Durch Datensätze browsen" title="Durch Datensätze browsen" /></a></td>
			<td><input type="hidden" name="'.$formName.'" value="'.$oldValues.'" /></td>
			</tr>
			</table>
		';

		return $this->makeRow(	$LANG->getLL('form_button_imageurl'), $cont );
	}

	/**
	 * enteredRequired()
	 * Inherit from tx_mailform_formAbstract
	 * @return unknown
	 */
	public function enteredRequired() {
		return true;
	}

  	/**
  	 * getFieldValue()
  	 *
  	 * @return String
  	 */
	public function getFieldValue() {
		return $this->configData['input_field_value'];
	}

	/**
	 * validFieldPost()
	 * Inherit from tx_mailform_formAbstract
	 * @return Boolean
	 */
	public function validFieldPost() { return true; }

	/**
	 * Inherit from tx_mailform_formAbstract
	 *
	 * @param unknown_type $mailid
	 */
	public function savePost($mailid) {
		$this->dbHan_saveField($mailid, '', $this->postData['submit'], '', '');
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_naviSubmitimage.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_naviSubmitimage.php']);
}