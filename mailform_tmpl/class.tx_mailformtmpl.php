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
require_once(t3lib_extMgm::extPath('mailform_tmpl')."lib/class.tx_mailformtmpl_display.php");
require_once(t3lib_extMgm::extPath('mailform_tmpl')."lib/class.tx_mailformtmpl_loader.php");
require_once(t3lib_extMgm::extPath('mailform_tmpl')."lib/class.tx_mailformtmpl_templateObj.php");
require_once(t3lib_extMgm::extPath('mailform_tmpl')."lib/class.tx_mailformtmpl_settings.php");
require_once(t3lib_extMgm::extPath('mailform_tmpl')."lib/class.tx_mailformtmpl_history.php");

class tx_mailformtmpl extends tx_mailform_parentWizard implements tx_mailform_mainInterface  {

	private $display;
	private $settings;
	private $controller;

	public static $critical_getVars = array('mftmpl_repl', 'extmft');

	public function __construct() {
		global $LANG;
		parent::init('mailform_tmpl');

		$this->display = tx_mailformtmpl_display::getInstance();
		$this->controller = tx_mailformtmpl_loader::getInstance();
		$this->settings = tx_mailformtmpl_settings::getInstance();
	}

	public function getDisplay() {
		return $this->display;
	}

	public function saveWizard() {
		$historyCount = tx_mailformtmpl_settings::getVariable('NR_SAVINGS');
		tx_mailformtmpl_history::getInstance()->saveHistory();
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform_tmpl/class.tx_mailformtmpl.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform_tmpl/class.tx_mailformtmpl.php']);
}
?>
