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
require_once(t3lib_extMgm::extPath('mailform').'lib/controller/class.tx_mailform_observer.php');
require_once(t3lib_extMgm::extPath('mailform').'tt_content_tx_mailform_config/singletons/class.tx_mailform_saveState.php');
require_once(t3lib_extMgm::extPath('mailform').'tt_content_tx_mailform_config/singletons/class.tx_mailform_configData.php');
require_once(t3lib_extMgm::extPath('mailform')."formTypesModel/class.tx_mailform_form.php");

/**
 * tx_mailform_formHandler
 * 
 * @author Sebastian Winterhalder <sw@internetgalerie.ch>
 *
 */
class tx_mailform_formHandler extends tx_mailform_observer {
	
	// class variables
	private $configData = array(array()); // Raw configuration array
	private $forms = array(); // An array of Instances of forms
	private $uniqueFieldName;
	private static $selfInstance;
	
	/**
	 * Private Constructor (Singleton Holder)
	 * Do only create this class over tx_mailform_tablefieldHandler::getInstance();
	 *
	 */
	private function __construct() {
		// Initialize variables
		$this->loadConfigData();
		$this->uniqueFieldName = tx_mailform_funcLib::getUniqueFieldname($this->configData);
	}

	public static function getInstance() {
		if(!isset(tx_mailform_formHandler::$selfInstance)) {
			tx_mailform_formHandler::$selfInstance = new tx_mailform_formHandler();
		}
		return tx_mailform_formHandler::$selfInstance;
	}

	
	/**
	 * Returns TRUE if one or more form-field is not used in the Wizard
	 *
	 * @return Boolean
	 */
	public function isAFormUnreferenced() {
		$x = 0;
		
		if(is_array($this->forms)) {
			foreach($this->forms as $formArr) {
				foreach($formArr as $form) {
					if(!$form->getForm()->isFormReferenceUsed()) {
						return true;
					}
				}
			}
		}
		return false;
	}

	public function loadConfigData() {
		$cfg = tx_mailform_configData::getInstance();
		$this->configData = $cfg->getConfigData();
		
		foreach($this->configData as $pIndex => $page) {
			foreach($page as $fIndex => $fCfg) {
				$formInstance = t3lib_div::makeInstance('tx_mailform_form');
				$formInstance->setupForm($fCfg, null, null);
				$this->forms[$pIndex][$fIndex] = $formInstance;
			}
		}
	}

	
	/**
	 * Get a specifig Form with ufid
	 *
	 * @param unknown_type $ufid
	 * @return unknown
	 */
	public function getForm($ufid) {
		foreach($this->forms as $pIndex => $page) {
			foreach($page as $fIndex => $form) {
				if($form->getForm()->getUFID() == $ufid && !$ufid == "") {
					return $form;
				}
			}
		}
		throw new Exception('Formular not found in Configuration: '.$ufid);
	}
	
	
	/**
	 * Add a form to all configuration
	 *
	 * @param unknown_type $ufid
	 */
	public function addForm($ufid, $fIndex=-1, $pIndex=-1) {
		$arr = false;
		// Form Data
		foreach($this->forms as $pIndex => $page) {
			foreach($page as $fIndex => $form) {
				if($form->getForm()->getUFID() == $ufid && !$ufid == "") {
					$arr = array($pIndex, $fIndex, $form);
				}
			}
		}
		
		if(!$arr) {
			$form = new tx_mailform_form();
			$configData['type'] = 'default';
			$configData['uName'] = $ufid;
			$form->setupForm($configData, null, null);
			
			$pIndex = ($pIndex == -1) ? 0 : $pIndex;
			$fIndex = count($this->configData[$pIndex]);

			$this->forms[$pIndex][$fIndex] = $form;
			$this->configData[$pIndex][$fIndex] = $form->getForm()->getConfigData();

			$this->sortArrays();
			$this->saveConfigData();
		}
	}
	
	/**
	 * Deletes the Form From all Configuration
	 *
	 * @param unknown_type $ufid
	 */
	public function removeForm($ufid) {
	
		foreach($this->forms as $pIndex => $page) {
			foreach($page as $fIndex => $form) {
				if($form->getForm()->getUFID() == $ufid && !$ufid == "") {
					$arr = array($pIndex, $fIndex, $form);
				}
			}
		}
				
		unset($this->forms[$arr[0]][$arr[1]]);
		unset($this->configData[$arr[0]][$arr[1]]);
		
		
		$keys = array_keys($this->configData);
		$configData = array();
		$formData = array();
		
		$pIndex = 0;
		foreach($this->configData as $key => $page) {
			$configData[$pIndex] = $page;
			$formData[$pIndex] = $this->forms[$key];
			$pIndex++;
		}
		$this->configData = $configData;
		$this->formData = $formData;
		
		$this->sortArrays();
		$this->saveConfigData();

		$tmtH = tx_mailform_tablefieldHandler::getInstance();

	}
	
	/**
	 *  Public function SortArrays();
	 *
	 *
	 */
	private function sortArrays() {
		$configData = array();
		$formData = array();
		foreach($this->configData as $pKey => $page) {
			$fIndex = 0;
			foreach($page as $fKey => $field) {
				$configData[$fIndex] = $field;
				$formData[$fIndex] = $this->forms[$pKey][$fKey];
				$fIndex++;
			}
			$this->configData[$pKey] = $configData;
			$this->forms[$pKey] = $formData;
		}
	}
	
	private function saveConfigData() {
		$tmcD = tx_mailform_configData::getInstance();
		$tmcD->setConfigData($this->configData);
		$this->updateObservables();
	}
	
	/**
	 * Used for General Changes of a Form Type
	 *
	 * @param String $ufid
	 * @param Array $formConfig
	 */
	public function loadFormConfig($ufid, $formConfig) {
		$form = $this->getForm($ufid);
		
		$form->getForm()->setConfigData($formConfig);
		
		foreach($this->configData as $pIndex => $page) {
			foreach($page as $fIndex => $field) {
				if($field['uName'] == $ufid)
					$this->configData[$pIndex][$fIndex] = $form->getForm()->getConfigData();
			}
		}
		
		$this->saveConfigData();
	}
	
	/**
	 * A new Unique Fieldname that does not exists
	 * The fieldname will change only every refresh
	 *
	 * @return String
	 */
	public function getUniqueFieldname() {
		return $this->uniqueFieldName;
	}
	
	/**
	 * Get the Config Data
	 *
	 * @return Array
	 */
	public function getConfigData() {
		return $this->configData;
	}
	
	/**
	 * Get All forms
	 *
	 * @return unknown
	 */
	public function getForms() {
		return $this->forms;
	}
	
	/**
	 * Get Field Configuration data
	 *
	 */
	public function getFieldData() {
		return $this->fieldData;
	}
	
	public function deleteUnusedForms() {
		foreach($this->forms as $pIndex => $p) {
			foreach($p as $fIndex => $form) {
				if(!$form->getForm()->isFormReferenceUsed())
					$this->removeForm($form->getForm()->getUFID());
			}
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/tt_content_tx_mailform_config/singletons/class.tx_mailform_formHandler.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/tt_content_tx_mailform_config/singletons/class.tx_mailform_formHandler.php']);
}
?>
