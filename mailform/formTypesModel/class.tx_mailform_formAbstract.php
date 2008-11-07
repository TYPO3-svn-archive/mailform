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
require_once(t3lib_extMgm::extPath("mailform")."formTypesModel/formAbstract/class.tx_mailform_formAbstract_FE.php");

/**
* tx_mailform_formAbstract
*
* @to add an additional form type, register the form type in tx_mailform_form.
*
* @author       Sebastian Winterhalder <sw@internetgalerie.ch>
*
*/
abstract class tx_mailform_formAbstract extends tx_mailform_formAbstract_FE {

	public $extType = "mailform";

	protected $configData;
	protected $statValue; // May be Delete, only used in getFormValue xx
	protected $_FieldValue;
	protected $formContent = array(); // Multidimensional array, in which the content is saved from the object


	protected $hasInitialized = false;
	protected $alreadyValidated = false;
	protected $fieldsPrefix;

	protected $requireBox = true; // Display require box
	protected $labelField = true; // Use Label in Configuration
	protected $singleUse = true; // Inherited form type can only once be included in fieldconfig
	protected $displayError = true; // Displays or Hides the FE-User Error (validation)

	protected $uniqueFieldname; // This string represents the exact uniquefieldname.
					                //This fieldname can only exists once in the configuration.
	protected $be_typeImage = '../gfx/type/standard.gif';
	protected $cObj;

	protected $templateObject;

	// Frontend Xajax Reference
	protected $xajax;

	// Frontend post data
	protected $postData = false;
	// Frontend field requires post when post is sent
	protected $postRequired = true;
	// Frontend field XML (new generated)
	protected $afterPostXML = false;

	protected $formPrefix = "tx_mailform";

	protected $labelLength = 30; // Value how long the length of the label is (including spaces)
	protected $input_field_cols = 30;  // Standard value for the FE Input fields
	protected $input_html_value;
	protected $input_error = array(true, "");

	private $bool_FormRefUsed = false;

	/**
	 * Initialize the Field
	 *
	 * @param $configData Array
	 * @param $fieldsPrefix String
	 * @return void
	 */
	public function init($configData, $fieldsPrefix, $cObj) {
		$this->cObj = $cObj;
		$this->configData = $configData;
		$this->fieldsPrefix = $fieldsPrefix;
		$this->fieldInit();
	}

	public abstract function enteredRequired();
	public abstract function validFieldPost();
	
	/**
	 * Access to this method, instead of validFieldPost();
	 * Wrapper for Inheritance
	 * 
	 * @return Boolean
	 */
	public function DEPRECATED_validField() {
		$feHandler = tx_mailform_FE_Handler::getInstance();
		$tblFields = $feHandler->getTableFields();
		
		// Gehe alle Felder bei aktueller Seite durch, um zu überprüfen ob ein
		// verstecktes element in der konfiguration liegt, welches nicht geprüft werden soll
		// Alle expressions die bei diesem Feld vorkommen werden im array abgelegt
		// und anschliessend getestet
		$parseExpressions = array();

		foreach($tblFields as $page) {
			foreach($page as $row) {
				foreach($row as $fieldElement) {
					if($fieldElement->getConditionActivated()) {
						$forms = $fieldElement->getFormElements();
						foreach($forms as $form) {
							if($form->getForm()->getUFID() == $this->getUFID()) {
								$parseExpressions[] = $fieldElement->getCondition();
							}
						}
					}
				}
			}
		}
		

		if($this->configData['display_field_condition_active'] == 'on') {
			$parseExpressions[] = $this->configData['display_field_condition'];
		}

		$checkingActive = true;
		foreach($parseExpressions as $expression) {
			require_once(t3lib_extMgm::extPath('mailform')."lib/templateParser/class.tx_mailform_parseEngine.php");
			$parseEngine = new tx_mailform_parseEngine();
			$parseEngine->loadData($expression);
			
			// If field is somewhere hidden, disable checking of values
			if($parseEngine->getParsed() == false || $parseEngine->getParsed() == 0) {
				$checkingActive = false;
			}
		}
		
		if($checkingActive) {
			return $this->validFieldPost();
			try {
				return true;
			} catch (Exception $e) {
				return true;
			}
		}
		else {
			/*print "Checking inactive <br>";
			print $this->getUFID();
			t3lib_div::debug($parseExpressions);
*/
			return true;
		}
	}
	
	/**
	 * returns an Array with contents to save in a DB
	 *
	 * @param unknown_type $mailid
	 */
	public function savePost($mailid) {
		$rawString = $this->postData[$this->configData['type']];
		return $this->dbHan_saveField($mailid, '', $rawString, '', '');
	}
	
	public abstract function getFieldValue();

	/**
	 * Setup FE Post
	 *
	 */
	public function DEPRECATED_setupFEPost() {

		/*
		print "setupFEPost() must be changed - the Post Array will be loaded from FE_Handler by now<br>";
		assert(isset($this->pageNr) && isset($this->fieldNr));
		$post = t3lib_div::_GP($this->formPrefix);
		if(!empty($post)) {
			$this->setPostData($post[$this->pageNr][$this->fieldNr]);
		}
		*/
	}

	/**
	 * Get Page where the Form is set
	 *
	 * @return Int
	 * @deprecated
	 */
	public function DEPRECATED_getPageNr() {
		return $this->pageNr;
	}
	
	/**
	 *  Get Field Label
	 *
	 * @return String
	 */
	public function getLabel() {
		return $this->configData['label'];
	}

	/**
	 * getFieldNr
	 *
	 * @return Int
	 */
	public function getFieldNr() {
		return $this->fieldNr;
	}

	/**
	 *  Get Unique Field ID
	 *
	 *@return String
	 * 
	 */
	public function getUFID() {
		if(!empty($this->configData['uName']))
			return $this->configData['uName'];
		else
			throw new Exception('Config Data is not properly Set: $this->configData[\'uName\']');
	}

	/**
	 * Get Standard Field Value
	 *
	 * @return String
	 * 
	 */
	public function getStandardValue() {
		if(!empty($this->configData['input_field_value']))
			return $this->configData['input_field_value'];
		else
			throw new Exception('Config Data is not properly Set: $this->configData[\'input_field_value\']');
	}
	
	/**
	 * setup current content ($dbFieldRow)
	 *
	 * @param String $dbFieldRow
	 */
	public function setupCurrentContent($dbFieldRow) {
		$this->_FieldValue = t3lib_div::makeInstance("tx_mailform_fieldValueContainer");
		$this->_FieldValue->setArray(array($dbFieldRow));
	}

	/**
	 * 
	 * Returns boolean if field shall displayed in stats at all
	 *
	 */
	public function isFieldInStats() {
		if(empty($this->configData['exclude_button_from_stats'])) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 *
	 * Get the current field content
	 *
	 */
	public function getCurrentContent() {
    	if(!empty($this->_FieldValue))
      		return $this->_FieldValue->getCSV();
      	else return '';
	}

	/**
	 * Returns whether the form is used in the field configuration or not
	 *
	 * @return Boolean
	 */
	public function isFormReferenceUsed() {
		assert(is_bool($this->bool_FormRefUsed));

		return $this->bool_FormRefUsed;
	}

	/**
	 * Set whether the form is used in the field configuration
	 *
	 * @param Boolean $boolean
	 */
	public function setFormReferenceUsed($boolean) {
		$this->bool_FormRefUsed = $boolean;
	}

	/**
	 * Returns the current form Type
	 *
	 */
	public function getFormType() {
		return $this->configData['type'];
	}

	/**
	 *  Returns the current config data
	 *
	 */
	public function getConfigData() {
		return $this->configData;
	}

	/**
	 * set Config Data
	 *
	 * @param Array $configData
	 */
	public function setConfigData($configData) {
		$this->configData = $configData;
	}

	public function containsAttachment() { return false; }
	protected abstract function fieldInit();
	
	protected function validateField() { 
		$this->alreadyValidated = true;
	}

	/**
	 * Save a field to the Statistics Database
	 *
	 * @param unknown_type $mailid
	 * @param unknown_type $content_text
	 * @param unknown_type $content_varchar
	 * @param unknown_type $content_blob
	 * @param unknown_type $content_int
	 * @return unknown
	 */
	public function dbHan_saveField($mailid, $content_text, $content_varchar, $content_blob, $content_int) {
		if($content_text != "") {
			$datatype = 'content_text';
		} elseif($content_varchar != "") {
			$datatype = 'content_varchar';
		} elseif($content_blob != "") {
			$datatype = 'content_blob';
		} elseif($content_int != "") {
			$datatype = 'content_int';
		} else {
			$datatype = 'empty';
		}
		
		assert(!empty($this->configData['uName']));
			$insertArray = array (
				'mailid' => $mailid,
				'ufid' => $this->configData['uName'],
				'content_text' => $content_text,
				'content_varchar' => $content_varchar,
				'content_blob' => $content_blob,
				'content_int' => $content_int,
				'field_type' => $this->getFormType(),
				'tstamp' => time(),
				'crdate' => time(),
				'data_type' => $datatype
			);
		return $insertArray;
	}

 	/**
 	 * Contains Email Receiver
 	 *
 	 * @return unknown
 	 */
	public function containsEmailReceiver() {
		if(!empty($this->configData['use_as_email']) || ($this->configData['use_as_email_choose'] != 'no_recipient' && !empty($this->configData['use_as_email_choose']))) {
			return t3lib_div::validEmail($this->getFieldValue());
		}
		else {
			return false;
		}
	}

	/**
	 * Returns type of email recipients
	 * Possible Values: false, user_recipient, admin_recipient, all_recipient
	 *
	 * @return String, Boolean
	 */
	public function getEmailReceiverType() {
		if($this->containsEmailReceiver()) {
			if(!empty($this->configData['use_as_email'])) {
				return 'user_recipient';
			}
			return $this->configData['use_as_email_choose'];
		} else {
			return false;
		}
	}

	/**
	 * getContentEmails();
	 *
	 * @return Array
	 */
	public function getContentEmails() {
		if($this->containsEmailReceiver())
			return array($this->getFieldValue());
		else
			return false;
	}

	/**
	* Returns the new generated xml when validated and
	*
	* @deprecated
	*/
	protected function getFieldXML() {
		if($this->afterPostXML !== false && $this->enteredRequired() && $this->validFieldContent()) {
			return $this->afterPostXML;
		}
		else {
			die("in function 'getFieldXML()' assertion failed, please revisit code");
		}
	}

	/**
	 * Returns if the post data is set
	 *
	 * @return boolean
	 */
	protected function postVarSet() {
		return ($this->postData !== false);
	}

	/**
	 * Set the post data
	 */
	public function setPostData($postData) {
		$this->postData = $postData;
	}

	/**
	 * @Return if the Field has been initialized
	 *
	 *@return Boolean
	 */
	public function hasInitialized() {
		return $this->hasInitialized;
	}

	/**
	 *  Returns whether Xajax is enabled or not
	 *
	 *@return Boolean
	 */
	public function xajaxEnabled() {
		require_once(t3lib_extMgm::extPath('mailform')."lib/database/class.tx_mailform_db_ttContentRow.php");
		return (tx_mailform_db_ttContentRow::getInstance()->getFlexformValue("sDEF","enableXajax") == 1);
	}

  /**
    *
    *@return Boolean
    */
	public function isFormRequired() {
		if(isset($this->configData)) {
			return (isset($this->configData['required']));
		} else {
			die("Config data is not set - Please set first the config data");
		}
	}

	/**
    *
    *
    */
	public function hasFormValidation() {
		if(isset($this->configData)) {
			if((isset($this->configData['validation_type']) && !($this->configData['validation_type'] == "no_validation"))) {
				return true;
			} else {
				return false;
			}
		}
		else {
			die("Config data is not set - Please set first the config data");
		}
	}

	protected function getStatusImageInfo() {
		global $LANG;
		$popup_info = '<b>'.$LANG->getLL('forms_type').'</b>:'.$this->getLLOfFormType().'<br><b>'.$LANG->getLL('info_label_key').'</b> ['.$this->configData['uName'].']<br>';
		if(empty($this->configData['label']))
			$popup_info .= '<b>Label:</b> '.$LANG->getLL('fWiz_label_empty')."<br>";
		if(strlen($this->configData['label']) > 20)
			$popup_info .= '<b>Label:</b> '.$this->configData['label']."<br>";
			
		return '<a style="pointer:none;" onmouseover="return overlib(\''.$popup_info.'\');" onmouseout="return nd();"><img style="cursor: help;" src="'.$this->be_typeImage.'" alt="" border="0"></a>';	
	}

	/**
	 * appendFormDataValid
	 *
	 * Append an Error Message
	 *
	 * @param Boolean $isValid
	 * @param String $appendErrormsg
	 */
	protected function appendFormDataValid($isValid, $appendErrormsg) {
		 if(strlen($this->input_error[1]) != 0)
		 	$this->input_error[1] .= "<br>";
		 $this->input_error[1] .= $appendErrormsg;
		// if($this->isFormValid())
		 $this->input_error[0] = $isValid;
	}

	/**
	 * isFormValid
	 *
	 * @return int
	 */
	protected function isFormValid() {
		$this->validateField();
		if($this->input_error[0] == false)
			return false;
		else return true;
	}

	/**
	 * getFormErrorMsg
	 *
	 * @return unknown
	 */
	protected function getFormErrorMsg() {
		return $this->input_error[1];
	}
	
	public function __toString() {

		$string = "tx_mailform_formAbstract[Type:".$this->configData['type']."|Label:".$this->configData['label'].""."] => $add";
		return $string;
	}
}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/class.tx_mailform_formAbstract.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/class.tx_mailform_formAbstract.php']);
}