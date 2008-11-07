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
require_once(t3lib_extMgm::extPath("mailform")."lib/database/class.tx_mailform_dbInstance.php");
require_once(t3lib_extMgm::extPath('mailform')."pi1/mail/class.tx_mailform_sendOperator.php");

/**
 *
 *
 *
 */   
class  tx_mailform_db_ttContentRow extends tx_mailform_dbInstance {
			
  static private $instance;
  private $query;
  private static $elementId;
  private $flexiArray;
  private $pageconfArray;
  private $currentLanguageId = 0;
  
  /**
   * Get Instance
   *
   *@param Object   
   */        
	static public function getInstance($elementId=0) {
		if(!self::$instance || (tx_mailform_db_ttContentRow::$elementId != $elementId && $elementId != 0)) {
			$elementId = empty($_GET['elmId']) ? $elementId : $_GET['elmId'];
			self::$instance = new tx_mailform_db_ttContentRow($elementId);
		}
		return self::$instance;
	}
  
  /**
   * Get RAW XML String
   *
   * @return String
   */
  public function getConfigData() {	
    return $this->rows[0]['tx_mailform_config'];
  }

  /**
   * Get Parsed Config Data
   *
   * @return Array
   */
  public function getConfigArray() {
  	return t3lib_div::xml2array($this->getConfigData());
  }
  
  public function getExtensionType() {
  	return $this->getFlexformValue('sDEF', 'mailform_type');
  }
  
  public function getElementID() {
  	return tx_mailform_db_ttContentRow::$elementId;
  }
  
  /**
   *  Flexform
   *
   */
  public function getFlexformXML() {
    return $this->rows[0]['pi_flexform'];
  }

  public function getFlexformArray() {
    if(!empty($this->flexiArray)) {
      return $this->flexiArray;
    }
    else
      throw new Exception("Flexform Array could not been found");
  }

  public function getFlexformValue($sheet, $key) {
    return $this->flexiArray[$sheet][$key];
  }

  /**
   * Private Constructor
   *
   */
  private function __construct($elementId) {
    tx_mailform_db_ttContentRow::$elementId = $elementId;
    $sys_language_uid = $GLOBALS['TSFE']->config['config']['sys_language_uid'];
    if(empty($sys_language_uid))
    	$sys_language_uid = 0;
    
    $this->query = "SELECT pi_flexform, tx_mailform_config FROM tt_content
											WHERE (uid = ".tx_mailform_db_ttContentRow::$elementId."
														OR  t3_origuid = ".tx_mailform_db_ttContentRow::$elementId.") 
													AND sys_language_uid = $sys_language_uid
													AND deleted = 0
											";
    $res = $GLOBALS['TYPO3_DB']->sql_query($this->query);
    $this->rows = array();
    
    while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
      $this->rows[] = $row;
    }

    if(count($this->rows) == 0) {
			$this->query = "SELECT pi_flexform, tx_mailform_config FROM tt_content
											WHERE uid = ".tx_mailform_db_ttContentRow::$elementId."";
			$res = $GLOBALS['TYPO3_DB']->sql_query($this->query);
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
      	$this->rows[] = $row;
    	}
		}
    
    $this->setStandardFlexform(); // Set standard Flexform in case the flexform has not yet created, standard will be overwritten
    $flexArr = t3lib_div::xml2array($this->getFlexformXML());
    
    if(is_array($flexArr)) {
	    foreach ($flexArr['data'] as $sheet=>$data) {
	      foreach($data as $lang=>$value) {
	        foreach($value as $key=>$val) {
	          $this->flexiArray[$sheet][$key] = $val['vDEF'];
	        }
	      }
	  	}
    }
    
    /*
    // Case dependant config data reading
	// LOAD CONFIGURATION DATA = Field Informations
	// Mailform type
	// 0 = For Thanks page use HTML Field
	// 1 = For Thanks page redirect to
	// 2 = Is a thanks page, get elements from origin url
	if($this->getExtensionType() == '0') {
		// Do Nothing, the right Config Data is already loaded
	} elseif ($this->getExtensionType() == '1') {
		// Do also nothing, the right config data is in the main page
	}
	else {
		//
		// Read new config data, the page is beeing redirected
		//
		$page_key = split(',', $this->getFlexformValue('sDEF', 'root_pages'), 1);
		$orig_page_key = $page_key[0];
		if(empty($orig_page_key)) {
			$sendOperator = tx_mailform_sendOperator::getInstance();
			$sendOperator->addError('Flexform Error: You have to define a origin page from where the content is redirected');
		}
		
		
		$reload_sql = "SELECT pi_flexform, tx_mailform_config, list_type FROM tt_content WHERE uid = '".$orig_page_key."' AND list_type = 'mailform_pi1'";
    	$res = $GLOBALS['TYPO3_DB']->sql_query($reload_sql);
    	$row = array();
    	while($rowTmp = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
      		$row[] = $rowTmp;
    	}
    	
    	if(count($row) <= 0) {
    		$sendOperator = tx_mailform_sendOperator::getInstance();
			$sendOperator->addError('Flexform Reference Error: The given Content Element is not a mailform plugin element');
		
    	}
    	
		$this->rows[0]['tx_mailform_config'] = $row[0]['tx_mailform_config'];
	}
	*/
  }
  
  private function setStandardFlexform() {
  	
  	$this->flexiArray['sDEF'] = array(
  			"sendername" => "",
  			"sender" => "",
  			"thanks_page" => "",
  			"enableXajax" => 0,
  			"templatePath" => "",
  			"cssPath" => ""
  		);
  	$this->flexiArray['s_mailconfig'] = array(
  			"html_allowed" => 1,
  			"subject" => "",
  			"mail_header" => "",
  			"mail_footer" => 0,
  			"recipient" => "",
  		);
  	$this->flexiArray['admin_mailconfig'] = array(
  			"html_allowed" => 1,
  			"subject" => "",
  			"mail_header" => "",
  			"mail_footer" => 0,
  			"recipient" => "",
  		);  	
  }
  
  public function getLanguageInfo() {
  	return array(
  			'language_id' => $this->currentLanguageId,
  			'' => ''
  		);
  }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/database/class.tx_mailform_db_ttContentRow.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/database/class.tx_mailform_db_ttContentRow.php']);
}
?>