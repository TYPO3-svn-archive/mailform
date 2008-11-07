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


require_once(t3lib_extMgm::extPath('mailform')."lib/wizardInterface/interface.tx_mailform_mainInterface.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/wizardInterface/class.tx_mailform_parentWizard.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/addonInterface/interface.tx_mailform_FE_Addon.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/addonInterface/interface.tx_mailform_BE_Addon.php");

require_once(t3lib_extMgm::extPath('mailform_statistics')."lib/class.tx_mailformstatistics_display.php");

/*
require_once(t3lib_extMgm::extPath('mailform_tmpl')."lib/class.tx_mailformtmpl_display.php");
require_once(t3lib_extMgm::extPath('mailform_tmpl')."lib/class.tx_mailformtmpl_loader.php");
require_once(t3lib_extMgm::extPath('mailform_tmpl')."lib/class.tx_mailformtmpl_templateObj.php");
require_once(t3lib_extMgm::extPath('mailform_tmpl')."lib/class.tx_mailformtmpl_settings.php");
require_once(t3lib_extMgm::extPath('mailform_tmpl')."lib/class.tx_mailformtmpl_history.php");

*/

class tx_mailformstatistics extends tx_mailform_parentWizard implements tx_mailform_FE_Addon, tx_mailform_BE_Addon {
	
	private $display;
	private $settings;
	private $controller;
	
	public static $critical_getVars = array('SET[function]');
	
	public function __construct() {
		global $LANG;
		parent::init('mailform_statistics');
		$this->display = tx_mailformstatistics_display::getInstance();
	}
	
	public function getDisplay() {
		return $this->display;
	}
	
	public function saveWizard() {
		// TODO
	}
	
	/**
	 * Frontend ADDON
	 *
	 */
	
	public function formularSent($arg=array()) {
		require_once(t3lib_extMgm::extPath('mailform')."/lib/class.tx_mailform_funcLib.php");
		
		$mailid = tx_mailform_funcLib::getUniqueMailid();
		$FE_Handler = tx_mailform_FE_Handler::getInstance();
		$flexForm = $FE_Handler->getConfigData()->getFlexform();

		$insertArray = array (
			'tstamp' => time(),
			'mailid' => $mailid,
			'formid' => $FE_Handler->getMailformUID(),
			'recipient' => implode(",", $arg['visitor_receiver']),
			'recipient_admin' => implode(",", $arg['admin_receiver']),
			'subject' => $flexForm['admin_mailconfig']['subject'],
			'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'],
			'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'],
			'REMOTE_PORT' => $_SERVER['REMOTE_PORT'],
		);

		$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_mailformstatistics_mails', $insertArray);
		
		$fields = $FE_Handler->getFieldElements(true);
		
		foreach($fields as $field) {
			foreach($field as $s1) {
				// Only save forms and navi types
				if($s1->isValidFormType($s1->getForm()->getFormType() || $s1->isValidNaviType($s1->getForm()->getFormType()))) {
					$saveArray = $s1->getForm()->savePost($mailid);
					
					if(empty($saveArray))
						die( $s1->getForm()->getFormType() );
					
					$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_mailformstatistics_stats', $saveArray);
				}
			}
		}
	}
}

/*
class tx_mailformstatistics extends tx_mailform_parentWizard implements tx_mailform_mainInterface  {


}
*/
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform_statistics/class.tx_mailformstatistics.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform_statistics/class.tx_mailformstatistics.php']);
}
?>