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
 * @author       Sebastian Winterhalder <typo3@internetgalerie.ch>
 */
class tx_mailform_naviSubmit extends tx_mailform_naviSubmitAbstract {
  
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
		$field = '<input id="'.$this->getUniqueIDName("input").'" class="tx_mailform_submit" type="submit" name="'.$this->getVarPrefix().'[submit][0]" value="'.$this->configData['form_button_value'].'" />
					<input type="hidden" name="'.$this->getVarPrefix().'[submit][1]" value="submit" />
					';

		return $this->getWithTemplate($this->configData['label'], $field);
	}

	/**
	 * renderHtml()
	 *
	 * @return unknown
	 */
	protected function renderHtml() {
		global $LANG;
		$array = array();
		$array[] = $this->makeTitleRow($LANG->getLL('form_options'));
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
	 *
	 * @return String
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
	 *
	 * @param Integer $mailid
	 */
	public function savePost($mailid) {
		return $this->dbHan_saveField($mailid, '', $this->postData['submit'], '', '');
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_naviSubmit.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_naviSubmit.php']);
}