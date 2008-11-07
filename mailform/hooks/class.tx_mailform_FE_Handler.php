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
require_once(t3lib_extMgm::extPath('mailform').'tt_content_tx_mailform_config/singletons/class.tx_mailform_configData.php');
require_once(t3lib_extMgm::extPath('mailform').'tt_content_tx_mailform_config/singletons/class.tx_mailform_saveState.php');
require_once(t3lib_extMgm::extPath('mailform').'tt_content_tx_mailform_config/singletons/class.tx_mailform_tablefieldHandler.php');
require_once(t3lib_extMgm::extPath('mailform').'tt_content_tx_mailform_config/singletons/class.tx_mailform_formHandler.php');
require_once(t3lib_extMgm::extPath('mailform').'lib/templateParser/class.tx_mailform_parseEngine.php');
require_once(t3lib_extMgm::extPath('mailform').'hooks/class.tx_mailform_Handler.php');

/**
 * Provides functions which can be used for hooks and addons
 *
 */
class tx_mailform_FE_Handler extends tx_mailform_Handler {

	protected $templateParser;

	protected $flexformData; // Frontend Flexform Configuration
	protected $fieldElements = array(array()); // contains the form elements
	protected $tableFields = array(array()); // Contains the Fields / Page Structure
	protected $tablefieldHandler;
	protected $currentPage; // Contains the current page
	protected $submitPressed = false; // If submit button pressed this var will be true
	protected static $uid;
	
	/**
	 * get an instance of this class
	 *
	 * @return Object
	 */
	public static function getInstance($uid=0) {
		if(empty(self::$instance) || ($uid != 0 && $uid != tx_mailform_FE_Handler::$uid)) {
			tx_mailform_FE_Handler::$uid = $uid;
			self::$instance = new tx_mailform_FE_Handler(tx_mailform_FE_Handler::$uid);
		}
		return self::$instance;
	}

	/**
	 * Get Content UID
	 *
	 * @return int
	 */
	public static function getContentUID() {
		return tx_mailform_FE_Handler::$uid;
	}
	
	/**
	 * register Addon
	 *
	 * @param unknown_type $instance
	 */
	public function registerAddon(tx_mailform_addon $addon) {
		
	}
	
	/**
	 * Constructor
	 *
	 */
	protected function __construct() {
		if(TYPO3_MODE != 'FE')
			throw new Exception('This class FE_Handler can only be used if used in Frontend Plugins');
		parent::__construct();
		$this->saveState = tx_mailform_saveState::getInstance();
		$this->templateParser = tx_mailform_templateParser::getInstance();
		$this->configData = tx_mailform_configData::getInstance($this->uid);
		$this->tablefieldHandler = tx_mailform_tablefieldHandler::getInstance();
		$this->loadFields();
		$this->loadData();
	}
	
	/**
	 * set the Plugin Reference
	 *
	 * @Param Object $ObjRef
	 */
	public function setP1Reference($ObjRef) {
		$this->pi1_reference = $ObjRef;
	}

	/**
	 * getConfigData
	 *
	 * @return Array
	 */
	public function getConfigData() {
		$this->ensureInitialized();
		return $this->configData;
	}
	
	/**
	 * getFlexformData
	 *
	 * @return Array
	 */
	public function getFlexformData() {
		$this->ensureInitialized();
		return $this->flexformData;
	}
	
	/**
	 * ensureInitialized
	 * Returns false if any of the class variable is empty
	 *
	 * @return Boolean
	 */
	public function ensureInitialized() {
		
		$flag = true;
		if(empty($this->saveState)) {
			$error .= " SaveState Reference not set";
			$flag = false;
		}
		if(empty($this->templateParser)) {
			$error .= " Template Parser Reference not set";
			$flag = false;
		}
		if(empty($this->configData)) {
			$error = "Config Data Reference not set";
			$flag = false;
		}
		if(!$flag)
			throw new Exception("FE_Handler is not properly initialized: ".$error);
	}
	
	/**
	 * get Template Parser
	 *
	 * @return unknown
	 */
	public function getTemplateParser() {
		if(empty($this->templateParser))
			$this->templateParser = tx_mailform_templateParser::getInstance();
		return $this->templateParser;
	}
	
	/**
	 * get the Plugin Reference
	 *
	 * @return Object
	 */
	public function getP1Reference() {
	  if(!empty($this->pi1_reference))
			return $this->pi1_reference;
		else
			throw new Exception('Plugin reference is not set');
	}
	
	/**
	 * get the current page in FE
	 *
	 *@return 0
	 */
	public function getCurrentPage() {
		if(!isset($this->currentPage))
			$this->currentPage = 0;
		
		return $this->currentPage;
	}

	/**
	 * Handle Page Navigation
	 *
	 */
	public function handlePageNavigation() {
		global $plugin_configuration;

		// Set Page
		$P = $this->getPageNaviInformations();
		
		$pageConfig = tx_mailform_configData::getInstance()->getPageConfig();

		foreach($P as $key => $arrValue) {
			 if($key != 'current' && isset($arrValue['navigation'])) {
			 	$arrValue = $arrValue['navigation'];
			 	if(isset($arrValue['direct'][0]) && isset($arrValue['direct'][1])) {
			 		// Check if Form can be sent invalid or must be valid
			 		$cfgData = tx_mailform_configData::getInstance()->getPageConfig();
			 		$pageSwap = true;
			 		if(!empty($cfgData[$P['current']]['singlevalidation'])) {
			 			foreach($this->tableFields[$P['current']] as $row) {
			 				foreach($row as $fieldElement) {
			 					$forms = $fieldElement->getFormElements();
			 					foreach($forms as $form) {
				 					if(!$form->validField()) {
				 						$pageSwap = false;
				 						break;
				 					}
			 					}
			 				}
			 			}
			 		}
			 		
			 		// If Page swap is True, the page has been completely validated or does not need to be
			 		if($pageSwap) {
			 			if($arrValue['direct'][1] < 0) {
							$page = 0;
			 			} elseif($arrValue['direct'][1] >= $this->getPagesCount()) {
							$page = ($this->getPagesCount()-1);
			 			} else {
							$engine = new tx_mailform_parseEngine();
							$engine->loadData($cfgData[$arrValue['direct'][1]]['pagecondition']);
							
							if($engine->getParsed() != '0')
								$page = $arrValue['direct'][1];
							else {
								if($arrValue['direct'][1] > $P['current']) {
									if($cfgData[$arrValue['direct'][1]]['alternativepage'] == 'nextpage') {
										if($this->getPagesCount() > $arrValue['direct'][1]+1)
											$page = $arrValue['direct'][1]+1;
										else $page = $P['current'];
									} elseif($cfgData[$arrValue['direct'][1]]['alternativepage'] == 'lastpage') {
										$page = ($this->getPagesCount()-1); 
									} else {
										$page = $cfgData[$arrValue['direct'][1]]['alternativepage'];
									}
								} else  {
									$sEngine = new tx_mailform_parseEngine();
									if($cfgData[$arrValue['direct'][1]]['alternativepage_back'] == 'prevpage') {
										$redirect = $arrValue['direct'][1]+1;
										do {
											$redirect--;
											$sEngine->loadData($cfgData[$redirect]['pagecondition']);
										} while( $sEngine->getParsed() == 0 && $redirect > 0 );
										$page = $redirect;
									} elseif ($cfgData[$arrValue['direct'][1]]['alternativepage_back'] == 'firstpage') {
										$page = 0;
									} else {
										$page = $cfgData[$arrValue['direct'][1]]['alternativepage_back'];
									}
								}
							}
						}
					} else {
			 			$page = $P['current'];
			 		}
			 		$_SESSION['tx_mailform'][$this->getMailformUID()]['navigation']['current_page'] = $page;
			 	}
			 	
			 	// Reset Formular if needed
			 	if(isset($arrValue['reset'][0]) && isset($arrValue['reset'][1])) {
			 		$this->resetFormular();
			 	}
			 	
			 	if(isset($arrValue['submit'][0]) && isset($arrValue['submit'][1])) {
			 		$this->submitPressed = true;
			 	}
			 } else {
			 		if(isset($_SESSION['tx_mailform'][$this->getMailformUID()]['navigation']['current_page']))
			 			$page = $_SESSION['tx_mailform'][$this->getMailformUID()]['navigation']['current_page'];
			}
		}
		if(!isset($page)) {
			$page = 0;
		}
		
		$this->currentPage = $page;
	}
	
	/**
	 * get amount of pages
	 *
	 * @return unknown
	 */
	public function getPagesCount() {
		$this->ensureInitialized();
		
		return count($this->tableFields);
	}
	
	/**
	 * returns true if a submit button has been pressed
	 *
	 * @return boolean
	 */
	public function isSubmitted() {
		return $this->submitPressed;
	}

	/**
	 * get Page Navigation from Post
	 *
	 * @return Array
	 */
	private function getPageNaviInformations() {
		$Z = t3lib_div::_GP('tx_mailform');
		$P = $Z[$this->getMailformUID()];
		
		if(!isset($P['current'])) {
			if(!isset($Z['current']))
				$P['current'] = 0;
			else
				$P['current'] = $Z['current'];
		}
			
		return $P;
	}

	/**
	 * Checks if the current form with ufid is displayed
	 * Checks on all Pages (also not displayed)
	 * -- False if unreferenced
	 * -- True if Referenced in form
	 *
	 *
	 */
	public function isFormInDisplay($ufid) {
		foreach($this->tableFields as $page => $fieldPage) {
			foreach($fieldPage as $rowKey => $row) {
			  foreach($row as $fKey => $field) {
					$arr = $field->getFormElements();
					foreach($arr as $k) {
						if($k->getForm()->getUFID() == $ufid)
						  return true;
					}
				}
			}
		}
		return false;
	}
	
	/**
	 * all pages filled
	 *
	 * @return unknown
	 */
	public function allPagesFilled() {
		if($this->hasUserMadeChanges()) {
			$arr = $this->getSessionArray();
			foreach($arr as $page) {
				if(!isset($page['data_input']) || $page['data_input'] == false)
					return false;
			}
			return true;
		} else {
			return false;
		}
	}
	
	public function setPageSet($page) {
		$_SESSION['tx_mailform'][$this->P['uid']]['fe_content'][$_POST['tx_mailform_unique_user_id']][$page]['data_input'] = true;
	}
	
	/**
	 * Load Data into Object: Post Data or Original Data
	 *
	 */
	protected function loadData() {
		$P = t3lib_div::_GP('tx_mailform');

		$post = $P[$this->getMailformUID()];
		// Save Sent Data in Session
		if(!empty($post)) {
			foreach($post as $key => $postElement) {
				$_SESSION['tx_mailform'][$this->getMailformUID()]['fe_content'][$key] = $postElement;
			}
		}
		
		if(!$this->hasUserMadeChanges()) {
			// Formular is Empty or the User has made a mistake
		} else {
			$sess_data = $_SESSION['tx_mailform'][$this->getMailformUID()]['fe_content'];
			foreach($this->fieldElements as $rowKey => $row) {
				
				foreach($row as $elementKey => $element) {
					$flag = false;
					if(!empty($sess_data[$element->getForm()->getUFID()])) {
						$element->getForm()->setPostData($sess_data[$element->getForm()->getUFID()]);
						$flag = true;
					}
					if(!empty($P[$element->getForm()->getUFID()])) {
						$element->getForm()->setPostData($post[$element->getForm()->getUFID()]);
						$flag = true;
					}
				}
			}
		}
	}
	
	/**
	 * Load the Field data
	 *
	 */
	private function loadFields() {
		$cfgData = "";
    	$configData = $this->getConfigData()->getTotalConf();
    	$field = array();
    	
		foreach($configData['mailform_config'] as $key=> $field) {
			$fields[$field[tx_mailform_field::$fieldPrefix."page"]][$field[tx_mailform_field::$fieldPrefix."rowIndex"]][$field[tx_mailform_field::$fieldPrefix."colIndex"]] = new tx_mailform_field($field[tx_mailform_field::$fieldPrefix."rowIndex"], $field[tx_mailform_field::$fieldPrefix."colIndex"], $field[tx_mailform_field::$fieldPrefix."page"]);
			$fields[$field[tx_mailform_field::$fieldPrefix."page"]][$field[tx_mailform_field::$fieldPrefix."rowIndex"]][$field[tx_mailform_field::$fieldPrefix."colIndex"]]->loadFromConfig($field);
		}
		$this->tableFields = $fields;
		
		// Display each page
		foreach($configData['mailform_forms'] as $pageNr => $pageConf) {
			// Display fields for each page
			foreach($pageConf as $fieldNr => $fieldConf) {
				$this->fieldElements[$pageNr][$fieldNr] = t3lib_div::makeInstance('tx_mailform_form');
				$this->fieldElements[$pageNr][$fieldNr]->setupForm($fieldConf, $pageConf['type'].'-', $this->cObj);
			}
		}
		
		// Set the forms reference in the tablefields
		foreach($this->tableFields as $pageKey => $page) {
			foreach($page as $rowKey => $row) {
				foreach($row as $colKey => $element) {
					$element->setForms($this->getArrayOfForms($element->getContainingFormKeys()));
				}
			}
		}
	}
	
	/**
	 * Enter description here...
	 *
	 * @param Array $arrayKeys
	 * @return Array
	 */
	public function getArrayOfForms($arrayKeys) {
		assert(is_array($arrayKeys));
		$array = array();
		foreach($arrayKeys as $key) {
			foreach($this->fieldElements as $row) {
				foreach($row as $col) {
					if($col->getForm()->getUFID() == $key)
						$array[] = $col;
				}
			}
		}
		return $array;
	}
	
	public function getFormWithUFID() {
		
	}

	/**
	 * Returns the Field Elements (Forms)
	 *
	 * @return Array
	 */
	public function getFieldElements() {
		return $this->fieldElements;
	}
	
	/**
	 * Returns the Fields and Page Structure
	 *
	 * @return Array
	 */
	public function getTableFields() {
		return $this->tableFields;
	}
	
	/**
	 * getFormUserID()
	 *
	 * @return String
	 */
	public function getFormUserID() {
		if(!isset($_POST['tx_mailform_unique_user_id'])) {
			// Formular is Empty or the User has made a mistake
			$uid = $this->generateUid($this->getMailformUID());
			return $uid;
		} else {
			return $_POST['tx_mailform_unique_user_id'];
		}
	}
	
	/**
	 * getSession Array
	 *
	 * @return Mixed
	 */
	public function getSessionArray() {
		if($this->hasUserMadeChanges())
			return $_SESSION['tx_mailform'][$this->P['uid']]['fe_content'][$_POST['tx_mailform_unique_user_id']];
		else return false;
	}
	
	/**
	 * Get Mailform Unique ID
	 * ID from tt_content Database
	 *
	 * @return Integer
	 */
	public function getMailformUID() {
		return intval(tx_mailform_FE_Handler::$uid);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param Integer $pageContentId
	 */
	public function generateUid($pageContentId) {
		$rec = "UID_".$pageContentId."_".rand(10,20);
		if(isset($_SESSION['tx_mailform'][$this->P['uid']]['fe_content'][$rec]) && $rec != 0) {
			return $this->generateUid();
		} else {
			return $rec;
		}
	}
	
	/**
	 * Has user made changes ?
	 *
	 * @return Boolean
	 */
	public function hasUserMadeChanges() {
		return !empty($_POST['tx_mailform_unique_user_id']);
	}
	
	/**
	 * This function is called when the formular
	 * is submitted. Dont use this method, use Addon Methods
	 *
	 */
	public function formularSubmit($arg=array()) {
		$extLoader = tx_mailform_extLoader::getInstance();
		$extLoader->sendFormular($arg);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/hooks/class.tx_mailform_FE_Handler.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/hooks/class.tx_mailform_FE_Handler.php']);
}
