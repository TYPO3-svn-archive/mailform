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

/**
* mailform module tt_content_tx_mailform_forms
*
* @author       Sebastian Winterhalder <sw@internetgalerie.ch>
*/
class tx_mailform_form {
	// define extension type, but need to be changed later, maybe reference to the object who instantiate here.
	private $extType = "mailform";

	private $formReference; // This will be the placeholder for the instantiated referenceType
	private $availableTypes =	array('default',
  									'text',
									'textwdesc',
									'textarea',
									'select',
									'checkbox',
									'radio',
									'password',
									'file',
									'hidden',
									'contelement',
									'captcha',
									'staticcountry'); // Look $layoutTypes
 	private $layoutTypes =	array('default',
 									'separator',
									'title',
									'htmlelement',
									'error'); // Please keep 'default' as the first value in the array. All defaults will not be displayed in the Select boxes.
  	private $pageNaviTypes = array('default',
									'submit',
									'submitimage',
									'submitextended',
  									'nextpage',
  									'previouspage',
  									'reset'); //  pagenavi to add
  	private $cObj;

  /**
   *  Sets up the Internal Datatype with given standard values
   *
   *@param $formType // Give a type for the form to be created
   *@param Array $configData
   *@param fieldsPrefix // Give a prefix for the name. So the formparser will recognize the form
   */
	public function setupForm($configData, $fieldsPrefix, $cObj) {
		$this->cObj = $cObj;
		
		//t3lib_div::devLog('argh', $this->extType, 0, $configData);
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mailform']['useOwnFieldOptionsHook'])) {
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mailform']['useOwnFieldOptionsHook'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$hookIni = $_procObj->getIniValue();
				foreach ($configData as $key => $value){
					if (stristr($key, $hookIni)){
						$hookValue = $_procObj->useOwnFieldOptionsHook($value);
						$configData['input_field_value'] = $hookValue;
					}
				}
			}
		}
		$formType = $this->getTypeFromConfigData($configData);
		$this->createFormReference($formType);
		$this->formReference->init($configData, $fieldsPrefix, $this->cObj);
	}

	/** Create the Form */
	private function createFormReference($formType) {
		$formType = ($this->isValidFormType($formType)
						|| $this->isValidLayoutType($formType)
						|| $this->isValidNaviType($formType)) ? $formType : $this->availableTypes[0];
		$classPrefix = $this->getTypePrefix($formType);
		require_once(t3lib_extMgm::extPath($this->extType).'/formTypesModel/models/class.tx_mailform_'.$classPrefix.ucfirst($formType.".php"));
		require_once(t3lib_extMgm::extPath($this->extType).'/formTypesModel/class.tx_mailform_'.$classPrefix.'Abstract.php');
		$this->formReference = t3lib_div::makeInstance('tx_'.$this->extType.'_'.$classPrefix.ucfirst($formType));
	}

	/* Setup the sent post */
	public function setupPost($postData) {
		if(isset($this->formReference)) {
			$this->formReference->setPostData($postData);
		} else {
			die("The Form is not properly configured - Please setup form first");
		}
	}

	/**
	 * setupAssoc($assocArray)
	 *
	 * @param unknown_type $assocArray
	 */
	public function setupAssoc($assocArray) {
		$this->createFormReference($assocArray['field_type']);
		$this->formReference->setupAssoc($assocArray);
	}

  /**
   * returns the current class object
   *
   * @return Object   
   */        
	public function getForm() {
		return $this->formReference;
	}

  /**
   * Check if the given formtype is valid
   *
   *      
   *@return boolean
   **/        
	public function isValidFormType($formType) {
		if(array_search($formType, $this->availableTypes) !== false)
			return true;
		else
			return false;
	}
  
   /**
   * Check if the given layouttype is valid
   *
   *      
   *@return boolean
   **/   
	public function isValidLayoutType($formType) {
		if((array_search($formType, $this->layoutTypes) !== false) && $formType != 'default')
			return true;
		else
			return false;
	}
	
	/**
	 * Check if given formtype is a Formular Type
	 *
	 * @param String $formType
	 * @return Boolean
	 */
	public function isValidNaviType($formType) {
		if((array_search($formType, $this->pageNaviTypes) !== false) && $formType != 'default')
			return true;
		else
			return false;
	}
	
	public function getTypePrefix($formType) {
		if($this->isValidNaviType($formType))
			return 'navi';
		if($this->isValidLayoutType($formType))
			return 'layout';
		if($this->isValidFormType($formType))
			return 'form';
		return '';
	}
	
	/**
	 * Access to this method, instead of validFieldPost();
	 * Wrapper for Inheritance
	 * 
	 * @return Boolean
	 */
	public function validField() {
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
							if($form->getForm()->getUFID() == $this->getForm()->getUFID()) {
								$parseExpressions[] = $fieldElement->getCondition();
							}
						}
					}
				}
			}
		}
		
		$cfg = $this->getForm()->getConfigData();
		if($cfg['display_field_condition_active'] == 'on') {
			$parseExpressions[] = $cfg['display_field_condition'];
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
			return $this->getForm()->validFieldPost() && $this->getForm()->enteredRequired();
		}
		else {
			return true;
		}
	}
	
  /**
   *
   *Check if valid to send at email
   *
   */
	public function isValidToSend() {
		return $this->validField();
	}

  /**
   * Get the form type from the configData, The param must be an array
   * Which contains: $configData['type']
   *
   * @param Array
   * @return String // Form Type
   **/
	public function getTypeFromConfigData($configData) {
		if($this->isValidFormType($configData['type']) || $this->isValidLayoutType($configData['type']) || $this->isValidNaviType($configData['type']))
			return $configData['type'];
		else
			return false;
	}
  
  /**
   *  Returns an one dimensional array with all possible formtypes
   *
   *@return Array   
   */        
	public function getPossibleFormTypes() {
		return $this->availableTypes;
	}
  
  /**
   *  Returns an one dimensional array with all possible layout types
   *
   *@return Array   
   */   
	public function getPossibleLayoutTypes() {
		return $this->layoutTypes;
	}
	
	public function getPossibleNaviTypes() {
		return $this->pageNaviTypes;
	}
	
	public function __toString() {
		return 'tx_mailform_form => '.$this->formReference->__toString();
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/class.tx_mailform_form.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/class.tx_mailform_form.php']);
}
?>