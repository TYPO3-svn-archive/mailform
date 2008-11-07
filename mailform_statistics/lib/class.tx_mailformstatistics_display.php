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

require_once(t3lib_extMgm::extPath('mailform')."lib/class.tx_mailform_urlHandler.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/wizardInterface/interface.tx_mailform_displayInterface.php");

// Load HTML Library
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/table/class.tx_mailform_table.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/table/class.tx_mailform_td.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/table/class.tx_mailform_tr.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/form/class.tx_mailform_input.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/form/class.tx_mailform_textarea.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/form/class.tx_mailform_htmlform.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/form/class.tx_mailform_checkbox.php");

class tx_mailformstatistics_display implements tx_mailform_displayInterface {

	private static $display;
	private $urlHandler;
	
	public static function getInstance() {
		if(empty(self::$display)) {
			self::$display = new tx_mailformstatistics_display();
		}
		return self::$display;
	}
	
	private function __construct() {
		$this->urlHandler = new tx_mailform_urlHandler();
	}

	public function getWizardImage() {
		global $LANG;
		return '<img src="'.tx_mailform_parentWizard::getRelativePath('mailform_statistics').'wiz_icon.png" alt="Statistik" border="0">';
	}
	
	public function getContent() {
		header("LOCATION: ".tx_mailform_parentWizard::getRelativePath('mailform_statistics')."mod1/index.php?");
		return "Please open the 'E-Mail Statistics' Module in the Tools Bar";
	}
	
	public function extNavigation() {
		$urlHandler = new tx_mailform_urlHandler();
		$t = '
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td width="100%" style="padding-top: 2px; border-top:1px solid #80acff; border-left: 0px none #000; border-right: 0px none #000; border-bottom: 1px solid #80acff; background-color:#e5eeff;">
					<a href="'.$urlHandler->getCurrentUrl(tx_mailformtmpl::$critical_getVars, true, true).'&extmft=1"><img src="'.tx_mailform_parentWizard::getRelativePath('mailform_tmpl').'/gfx/save_template.png" alt="Save Current Template"></a>
					<a href="'.$urlHandler->getCurrentUrl(tx_mailformtmpl::$critical_getVars, true, true).'&extmft=0"><img src="'.tx_mailform_parentWizard::getRelativePath('mailform_tmpl').'/gfx/lupe.png" alt="List Templates"></a>
					<a href="'.$urlHandler->getCurrentUrl(tx_mailformtmpl::$critical_getVars, true, true).'&extmft=2"><img src="'.tx_mailform_parentWizard::getRelativePath('mailform_tmpl').'/gfx/settings.png" alt="Settings"></a>
					</td>
				</tr>
			</table>
		';
		return $t;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform_statistics/lib/class.tx_mailformstatistics_display.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform_statistics/lib/class.tx_mailformstatistics_display.php']);
}

?>