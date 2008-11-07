<?php
session_start();
/***************************************************************
*  Copyright notice
*
*  (c) 2006 Sebastian Winterhalder <sw@internetgalerie.ch>
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
require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(PATH_t3lib.'class.t3lib_htmlmail.php');
require_once(t3lib_extMgm::extPath('mailform')."/formTypesModel/class.tx_mailform_form.php");
require_once(t3lib_extMgm::extPath('mailform')."/formTypesModel/class.tx_mailform_xajaxHandler.php");
require_once(t3lib_extMgm::extPath("mailform")."/formTypesModel/class.tx_mailform_naviAbstract.php");
require_once(t3lib_extMgm::extPath('mailform')."/lib/templateParser/class.tx_mailform_templateParser.php");
require_once(t3lib_extMgm::extPath('mailform')."/lib/class.tx_mailform_urlHandler.php");
require_once(t3lib_extMgm::extPath('mailform')."/lib/layout/table/class.tx_mailform_table.php");
require_once(t3lib_extMgm::extPath('mailform')."/lib/class.tx_mailform_WizardWrapper.php");
require_once(t3lib_extMgm::extPath('mailform')."pi1/mail/class.tx_mailform_emailGenerator.php");
require_once(t3lib_extMgm::extPath('mailform')."pi1/mail/class.tx_mailform_sendOperator.php");
require_once(t3lib_extMgm::extPath('mailform')."tt_content_tx_mailform_config/model/class.tx_mailform_field.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/database/class.tx_mailform_db_ttContentRow.php");
require_once(t3lib_extMgm::extPath('mailform')."hooks/class.tx_mailform_FE_Handler.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/smtp/class.phpmailer.php");

error_reporting(E_ALL ^ E_NOTICE);

/**
 * Plugin 'mailform' for the 'mailform' extension.
 *
 * @author        Sebastian Winterhalder <sw@internetgalerie.ch>
 * 
 */
class tx_mailform_pi1 extends tslib_pibase {

	public $prefixId = 'tx_mailform_pi1';                // Same as class name
	public $scriptRelPath = 'pi1/class.tx_mailform_pi1.php';        // Path to this script relative to the extension dir.
	public $extKey = 'mailform';        // The extension key.

	private $confData; // Mainarray with all pages and fields
	private $flexiData = array();
	private $FieldElements = array();
	private $tableFields = array();
	
	public static $pageNaviPrefix = 'tx_mailform';

	/**
	 * The main method of the PlugIn
	 *
	 * @param        string                $content: The PlugIn content
	 * @param        array                $conf: The PlugIn configuration
	 * @return        The content that is displayed on the website
	 */
	function main($content,$conf) {
		global $cObj,$plugin_configuration, $FE_Handler;
		$cObj &= $this->cObj;

		$cfgData = tx_mailform_configData::getInstance($this->cObj->data['uid']);
		$this->conf = $conf;
		$plugin_configuration = $conf;
		
		$FE_Handler = tx_mailform_FE_Handler::getInstance($this->cObj->data['uid']);
	  	$FE_Handler->setP1Reference($this);
	  	$FE_Handler->handlePageNavigation();

		$this->flexiData = $cfgData->getFlexform();
		$this->confData = $cfgData->getTotalConf();
		
		$this->pi_loadLL(); // Loading language-labels
		$this->pi_setPiVarDefaults(); // Set default piVars from TS
		$this->pi_initPIflexForm(); // Init FlexForm configuration for plugin
		$GLOBALS['TSFE']->no_cache = 1;

		$templateParser = tx_mailform_templateParser::getInstance();
		
		/** Add CSS to Header */
		if((!isset($this->confData['mailform_forms']) || !isset($this->confData['mailform_config'])) && sizeof($this->confData > 0)) {
			$this->confData['mailform_forms'] = array();
			$this->confData['mailform_config'] = array();
			$sendOperator = tx_mailform_sendOperator::getInstance();
			$sendOperator->addError($this->pi_getLL('xml_format_error'));
		}

		if(!is_array($this->confData)) $this->confData = array();

		$content = $this->initElements();
		$this->loadCSS($conf);

		$flag = false;
		if($FE_Handler->isSubmitted()) {
			$flag = true;
			foreach($this->FieldElements as $pageNr => $pageElements) {
				foreach($pageElements as $fieldNr => $fieldElement) {
					
					if($FE_Handler->isFormInDisplay($fieldElement->getForm()->getUFID()) && !$fieldElement->isValidToSend()) {
						$flag = false;
					}
				}
			}
		}
		
		tx_mailform_xajaxHandler::getInstance()->processRequests();
		// add xajax javascript code to header
		$GLOBALS['TSFE']->additionalHeaderData['tx_mailform_pi1'] .= tx_mailform_xajaxHandler::getInstance()->getJavascript();

		$sendOperator = tx_mailform_sendOperator::getInstance();
		if($flag && $_SESSION['formAlreadySent'] !== true && $sendOperator->isSendable()) {
			$this->processSentForm();
			$FE_Handler->resetFormular();
			$_SESSION['formAlreadySent'] = true;
				if($sendOperator->isSendable())
					return $this->thanxPage();
				else
					return $this->getSendError('<h1>The E-Mail has not been sent</h1>Report this error to your website administrator<br>');
		} else {
			$_SESSION['formAlreadySent'] = false;
			if(!$sendOperator->isSendable()) {
				$content = $this->getSendError($content);
			}
			return $content;
		}
	}

	/**
	 * load frontend CSS
	 * 
	 * @version 0.8.5
	 * @param unknown_type $conf
	 */
	private function loadCSS($conf) {
		global $plugin_configuration;

		$ts_const = str_replace("EXT:", t3lib_extMgm::extPath('mailform'), $plugin_configuration[$key]);

		$cssPath = false;
		if(!empty($this->flexiData['sDEF']['css_path'])) {
			$ts_const = str_replace("EXT:", t3lib_extMgm::siteRelPath('mailform'), $this->flexiData['sDEF']['css_path']);
			if(file_exists($ts_const)) {
				$cssPath = $ts_const;
			}
			unset($ts_const);
		}

		if(!$cssPath && !empty($conf['css_path'])) {
			$ts_const = str_replace("EXT:", t3lib_extMgm::siteRelPath('mailform'), $conf['css_path']);
			if(file_exists($ts_const)) {
				$cssPath = $ts_const;
			}
			unset($ts_const);
		}

		if($cssPath) {
			$GLOBALS['TSFE']->additionalHeaderData['tx_mailform_pi1'] .= '<link rel="stylesheet" type="text/css" media="all" href="'.$cssPath.'" />';
		}
	}

	/**
	 * Returns Configuration Errors
	 *
	 * @param String $content
	 * @return String
	 */
	private function getSendError($content) {
		$sendOperator = tx_mailform_sendOperator::getInstance();
		$err = $sendOperator->getErrors();
		$string = '';
		foreach($err as $t) {
			$string = '<b>'.$t.'</b><br>';
		}
		$content = '<div style="color: #F00;">'.$string.'</div>'.$content;
		return $content;
	}
	
	/**
	 * Returns the Thanks Page
	 *
	 * @return String
	 */
	private function thanxPage() {
		$contentRow = tx_mailform_db_ttContentRow::getInstance();
		if($contentRow->getExtensionType() == '1') {
			$pid = $contentRow->getFlexformValue('sDEF', 'root_pages');

			header( 'LOCATION: http://'.t3lib_div::getIndpEnv('HTTP_HOST').'/'.$this->pi_getPageLink($pid) ) ;
		}

		return $this->flexiData['sDEF']['thanks_page'];
	}
	
	/**
	 * Parse Email CSV
	 *
	 * @param String $emailsString
	 * @return Array
	 */
	private function parseEmailCSV($emailsString) {
		$res = array();
		$arr = split(",", $emailsString);
		foreach($arr as $k => $elem) {
			$arr2 = split(";", $elem);
			foreach($arr2 as $k2 => $elem2) {
				$res[] = $elem2;
			}
		}
		return $res;
	}

	/**
	 * Function to create all elements configured in the backend
	 *
	 * @return        form with all elements
	 */
	private function initElements() {
		$FE_Handler = tx_mailform_FE_Handler::getInstance();
		
		$this->FieldElements = $FE_Handler->getFieldElements();
		$this->tableFields = $FE_Handler->getTableFields();

		$urlHandler = new tx_mailform_urlHandler();
		$tmp = $urlHandler->getCurrentUrl(array('id'));
		if($tmp == "?")
			$tmp .= "id=".$this->cObj->data['pid'];
		else
			$tmp .= "&id=".$this->cObj->data['pid'];
			$formUrl = $tmp;
			
		//$formUrl = $urlHandler->getCurrentUrl(array('id'))."&id=".$this->cObj->data['pid'];
		
		$formUrl = $this->pi_getPageLink($this->cObj->data['pid'],'',$_GET);
		//$formUrl = $_GET;
		
		$templateParser = tx_mailform_templateParser::getInstance();
		
		// Render result html
		return '
		<div>
		<form name="tx_mailform_'.$this->cObj->data['uid'].'" id="tx_mailform_'.$this->cObj->data['uid'].'" method="post" accept-charset="utf-8" enctype="multipart/form-data" action="'.$formUrl.'">
		<input type="hidden" name="tx_mailform_unique_user_id" value="'.$FE_Handler->getFormUserID().'" />
		<input type="hidden" name="'.tx_mailform_pi1::$pageNaviPrefix.'[current]" value="'.$FE_Handler->getCurrentPage().'" />
		'.$this->generateTable().'
		</form>
		</div>';
	}

	/**
	 * Generate Table
	 *
	 * @return String
	 */
	private function generateTable() {
	  global $FE_Handler;
	  
		if(!is_array($this->tableFields))
			$this->tableFields = array(array());

		$table = new tx_mailform_table();
		$table->setWidth("100%");
		$table->addCssClass('mailform-table-outer');
		
		if(count($this->tableFields[$FE_Handler->getCurrentPage()]) > 0) {
			foreach($this->tableFields[$FE_Handler->getCurrentPage()] as $rowKey => $row) {
				$tr = new tx_mailform_tr();
				foreach($row as $fieldKey => $field) {
				  if(!$field->isPlaceholder()){
						$tr->addTd($field->p1_getFieldBody($this->FieldElements));
					}
				}
				$table->addRow($tr);
			}
		}

		return $table->getElementRendered();
	}

	/**
	 * Process the sent form
	 *
	 */
	private function processSentForm() {
		global $FE_Handler;
		$emailGenerator = new tx_mailform_emailGenerator($this->cObj);
		$userEmails = $adminEmails = array();
		
		$listChecked = array(); // Contains all Handled Form-Keys
		foreach($this->tableFields as $key => $page) {
			foreach($page as $rowKey => $row) {
				foreach($row as $colKey => $element) {
					$forms = $element->getFormElements();
					foreach($forms as $form) {
						if(is_object($form) && $form->getForm()->containsEmailReceiver() && array_search($form->getForm()->getUFID(), $listChecked) === false) {
							if($form->getForm()->getEmailReceiverType() !== false) {
								$Emailtype = $form->getForm()->getEmailReceiverType();
			
								$TMPemail = $form->getForm()->getContentEmails();
			
								switch($Emailtype) {
									case 'admin_recipient':
										if(is_array($TMPemail))
											$adminEmails = array_merge($adminEmails, $TMPemail);
										else
											$adminEmails[] = $email;
									break;
									case 'user_recipient':
										if(is_array($TMPemail))
											$userEmails = array_merge($userEmails, $TMPemail);
										else
											$userEmails[] = $TMPemail;
									break;
									case 'all_recipient':
										if(is_array($TMPemail)) {
											$adminEmails = array_merge($adminEmails, $TMPemail);
											$userEmails = array_merge($userEmails, $TMPemail);
										}
										else {
											$adminEmails[] = $TMPemail;
											$userEmails[] = $TMPemail;
										}
									break;
									default:
										break;
								}
							}
						} else {
							if(!is_object($form))
							  trigger_error('Wrong Formular Object given', E_USER_WARNING);
							if($form->getForm()->containsAttachment()) {
								$filenames = $form->getForm()->getFilename();
								foreach($filenames as $file) {
									$FH = tx_mailform_fileHandler::getInstance();
									$FH->renameFileWithUID($file['uid']);
									$filename = $FH->getFilePath($file['uid']);
									$emailGenerator->addAttachment($filename);
								}
								$fileFormsReset[] = $form;
							}
						}
						$listChecked[] = $form->getForm()->getUFID();
					}
				}
			}
		}
		
		try {
			$emailGenerator->addReceivers( $userEmails );
		} catch (Exception $e) {
			// Email is invalid
			trigger_error("Incorrect parameters, arrays expected", E_USER_WARNING);
		}

		//$emailGenerator->setFields($FE_Handler->getFieldElements(), $FE_Handler->getFieldElements());

		// Send visitor Email
		$emailGenerator->setHtmlMail($this->flexiData['s_mailconfig']['html_allowed'] == 1);
		$emailGenerator->setSubject($this->flexiData['s_mailconfig']['subject']);

		$emailGenerator->setSender($this->flexiData['s_mailconfig']['s_sender_email']);
		$emailGenerator->setSenderName($this->flexiData['s_mailconfig']['s_sender_name']);
		$emailGenerator->hideFormData($this->flexiData['s_mailconfig']['display_data']);
		$emailGenerator->setContentHeader($this->flexiData['s_mailconfig']['mail_header']);
		$emailGenerator->setContentFooter($this->flexiData['s_mailconfig']['mail_footer']);
		try { $emailGenerator->addReceiversCSV($this->flexiData['s_mailconfig']['recipient']); }
		catch (Exception $e) {
			// Empty Receiver
			// Flexform Reciever will not be added.
		}

		$sendOperator = tx_mailform_sendOperator::getInstance();
		if($sendOperator->isSendable())
			$emailGenerator->send();
	
		$visitor_receiverArr = $emailGenerator->getReceivers();

		$emailGenerator->clearReceiver();
		// Send Administrator Email

		// Reset sender
		$emailGenerator->setSender($this->flexiData['s_mailconfig']['admin_sender_email']);
		$emailGenerator->setSenderName($this->flexiData['s_mailconfig']['admin_sender_name']);
		
		$emailGenerator->hideFormData($this->flexiData['admin_mailconfig']['display_data']);
		$emailGenerator->setHtmlMail($this->flexiData['admin_mailconfig']['html_allowed'] == 1);
		$emailGenerator->setSubject($this->flexiData['admin_mailconfig']['subject']);
		$emailGenerator->setContentHeader($this->flexiData['admin_mailconfig']['mail_header']);
		$emailGenerator->setContentFooter($this->flexiData['admin_mailconfig']['mail_footer']);

		try {
			$emailGenerator->addReceivers( $adminEmails );
		} catch (Exception $e) {
			// Email is invalid
			trigger_error("Incorrect parameters, arrays expected", E_USER_WARNING);
		}

		try {
			$emailGenerator->addReceiversCSV($this->flexiData['admin_mailconfig']['recipient']);
		} catch (Exception $e) {
			// Silent Error Catching, no Flexform Reciever given
			// Mail will not be generated
		}

		$admin_receiverArr = $emailGenerator->getReceivers();

		if($sendOperator->isSendable() && count($admin_receiverArr) > 0) {
			$emailGenerator->send();
		}

		if(empty($fileFormsReset))
			$fileFormsReset = array();
		// Reset Files
		foreach($fileFormsReset as $form) {
			$filenames = $form->getForm()->getFilename();
			foreach($filenames as $file) {
				$FH = tx_mailform_fileHandler::getInstance();
				$FH->renameFileWithUID($file['uid'], true);
			}
		}
		$admin_receiverArr = $emailGenerator->getReceivers();

		$arg_array = array('visitor_receiver' => $visitor_receiverArr, 'admin_receiver' => $admin_receiverArr);
		// Tell the Addons that the formular is sent
		if($sendOperator->isSendable()) {
			$FE_Handler->formularSubmit($arg_array);
		}
	}

	/**
	 * xajax function for changing pages in multipage forms
	 *
	 * @param         integer                $to_page:  page to change to
	 * @param         integer                $from_page:  old page
	 * @return        xajax response object
	 */
	function changePage($to_page,$from_page)	{
		$objResponse = new tx_xajax_response();
		for($i = 0; $i < $this->pages; $i++) {
			if($i == $to_page){
				// remove link and add style to actual page link
				$objResponse->addRemoveHandler('menu_page_'.$i, 'onclick', '');
				$objResponse->addAssign('menu_page_'.$i, 'className', 'pageMenuActive');
			} else {
				// update xajax function call and remove style
				$objResponse->addEvent('menu_page_'.$i, 'onclick', 'xajax_changePage('.$i.','.$to_page.')');
				$objResponse->addClear('menu_page_'.$i, 'className');
			}
		}
		// make actual page visible and make old page invisible
		$objResponse->addAssign('page_'.$from_page, 'className', 'invisible');
		$objResponse->addAssign('page_'.$to_page, 'className', 'visible');
		//return the XML response
		return $objResponse->getXML();
	}

	/**
	 * Get a formated error message
	 * */
	public function getErrorDialog($errorMessageLL_label) {
		return "<div>".$this->pi_getLL($errorMessage)."</div>";
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/pi1/class.tx_mailform_pi1.php'])        {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/pi1/class.tx_mailform_pi1.php']);
}