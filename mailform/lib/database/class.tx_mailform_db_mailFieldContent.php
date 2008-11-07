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
 * class tx_mailform_db_mailFieldContent
 *
 *
 */   
class  tx_mailform_db_mailFieldContent extends tx_mailform_dbInstance {
			
	static private $instance;
	private $query;
	
	/**
	 * Get Instance
	 *
	 *@param Object   
	 */        
	static public function getInstance() {
		if(!self::$instance) {
			self::$instance = new tx_mailform_db_mailFieldContent();
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
			/** Data Input */
			$sql = "SELECT tx_mailformstatistics_mails.*, tx_mailformstatistics_stats.*
			FROM tx_mailformstatistics_mails
			LEFT JOIN tx_mailformstatistics_stats ON tx_mailformstatistics_mails.mailid = tx_mailformstatistics_stats.mailid
			WHERE formid = '".$_GET['elmId']."'
			AND tx_mailformstatistics_stats.mailid = '".$_GET['item']."'
			";
		
			$res = $GLOBALS['TYPO3_DB']->sql_query($sql);
		
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$this->rows[] = $row;
			}
		}
	}
	
	public function getRowWithMailid($mailid) {
		foreach($this->rows as $row) {
		if($row['mailid'] == $mailid)
			return $row;
		}
		return false;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/database/class.tx_mailform_db_mailFieldContent.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/database/class.tx_mailform_db_mailFieldContent.php']);
}
?>