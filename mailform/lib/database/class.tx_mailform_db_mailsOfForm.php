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
/**
 *
 *
 *
 */   
class  tx_mailform_db_mailsOfForm extends tx_mailform_dbInstance {
	
	static private $instance;
	private $query;
	
	/**
	* Get Instance
	*
	*@param Object   
	*/        
	static public function getInstance() {
		if(!self::$instance) {
			self::$instance = new tx_mailform_db_mailsOfForm();
		}
	
		return self::$instance;
	}
  
	/**
	* Private Constructor
	*
	*/
	private function __construct() {
		$this->rows = array();
		
		if(t3lib_extMgm::isLoaded('mailform_statistics')) {
			$this->query = 'SELECT * FROM tx_mailformstatistics_mails WHERE formid = '.$_GET['elmId'];
			$res = $GLOBALS['TYPO3_DB']->sql_query($this->query);
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$this->rows[] = $row;
			}
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/database/class.tx_mailform_db_mailsOfForm.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/database/class.tx_mailform_db_mailsOfForm.php']);
}
?>