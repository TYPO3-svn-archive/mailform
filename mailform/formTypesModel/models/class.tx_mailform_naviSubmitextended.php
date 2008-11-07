<?php
/***************************************************************
* Copyright notice
*
* (c) 2007 Sebastian Winterhalder <sw@internetgalerie.ch>
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
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
require_once(t3lib_extMgm::extPath("mailform")."/formTypesModel/class.tx_mailform_naviSubmitAbstract.php");

/**
* mailform module tt_content_tx_mailform_forms
*
* @author Sebastian Winterhalder <sw@internetgalerie.ch>
*/
class tx_mailform_naviSubmitextended extends tx_mailform_naviSubmitAbstract {
 
	// Frontend field requires post when post is sent
	// Overwrite from mailform_formAbstract
	protected $postRequired = false;
	protected $requireBox = false;
	protected $be_typeImage = '../gfx/type/submit.gif';
	protected $javascript_onclick;
	protected $javascript_onmouseover;
	protected $javascript_onmouseout;
	protected $javascript_script;

	/**
	 * renderFrontend()
	 *
	 * @return String
	 */
	protected function renderFrontend() {
		$jsInclude = "";
		if(!empty($this->configData['javascript_onclick'])) {
			$this->configData['javascript_onclick'] = tx_mailform_funcLib::removeQuotationmark($this->configData['javascript_onclick']);
			$jsInclude .= ' onclick="'.$this->configData['javascript_onclick'].'"';
		}
		if(!empty($this->configData['javascript_onmouseover'])) {
			$this->configData['javascript_onmouseover'] = tx_mailform_funcLib::removeQuotationmark($this->configData['javascript_onmouseover']);
			$jsInclude .= ' onclick="'.$this->configData['javascript_onmouseover'].'"';
		}
		if(!empty($this->configData['javascript_onmouseout'])) {
			$this->configData['javascript_onmouseout'] = tx_mailform_funcLib::removeQuotationmark($this->configData['javascript_onmouseout']);
			$jsInclude .= ' onclick="'.$this->configData['javascript_onmouseout'].'"';
		}
		
		$field = "";
		if(!empty($this->configData['javascript_script'])) {
			$this->configData['javascript_script'] = tx_mailform_funcLib::removeQuotationmark($this->configData['javascript_script']);
			$field .= '<script language="javascript" type="text/javascript">'.$this->configData['javscript_script'].'</script>';
		}
			
			$field = '<input id="'.$this->getUniqueIDName("input").'" class="tx_mailform_submitextended" type="submit" name="'.$this->getVarPrefix().'[submit][0]" value="'.$this->configData['input_field_value'].'"'.$jsInclude.' />
					<input type="hidden" name="'.$this->getVarPrefix().'[submit][1]" value="submit" />
					';
			
		return $this->getWithTemplate($this->configData['label'], $field);
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
		$array[] = $this->makeRow($LANG->getLL('form_submit_js_onclick'), $this->makeInputField('javascript_onclick', $this->javascript_onclick));
		$array[] = $this->makeRow($LANG->getLL('form_submit_js_onmouseover'), $this->makeInputField('javascript_onmouseover', $this->javascript_onmouseover));
		$array[] = $this->makeRow($LANG->getLL('form_submit_js_onmouseout'), $this->makeInputField('javascript_onmouseout', $this->javascript_onmouseout));
		$array[] = $this->makeRow($LANG->getLL('form_submit_js_script'), $this->makeTextarea('javascript_script', $this->javascript_script));
		$array[] = $this->row_preFormButtonValue();
		$array[] = $this->row_preExcludeFromStats();

		//$array[] = $this->displayOptions();
		return $array;
	}

	/**
	 * enteredRequired()
	 *
	 * @return Boolean
	 */
	public function enteredRequired() {
		return true;
	}

	/**
	 * getFieldValue()
	 * Inherit from tx_mailform_formAbstract
	 * 
	 * @return Mixed
	 */
	public function getFieldValue() {
		return $this->configData['input_field_value'];
	}
	
	/**
	 * validFieldPost()
	 * Inherit from tx_mailform_formAbstract
	 *
	 * @return Boolean
	 */
	public function validFieldPost() { return true; }
 
	/**
	 * savePost($mailid)
	 * Inherit from tx_mailform_formAbstract
	 * 
	 * @param unknown_type $mailid
	 */
	public function savePost($mailid) { 
		$this->dbHan_saveField($mailid, '', $this->postData['submit'], '', '');
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_naviSubmitextended.php']) {
 include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_naviSubmitextended.php']);
}
?>