<?php
/**
* Attribute Factory Class. Every Entity does manage his attributes with that factory
*
* PHP versions 4 and 5
*
* Copyright notice
*
* (c) 2007 Sebastian Winterhalder <sebi@concastic.ch>
* All rights reserved
*
* This script is part of the TYPO3 project. The TYPO3 project is
* free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
*
*/
require_once(t3lib_extMgm::extPath('mailform').'lib/layout/interface/class.tx_mailform_parent.php');
require_once(t3lib_extMgm::extPath('mailform').'lib/layout/interface/interface.tx_mailform_I_content.php');
require_once(t3lib_extMgm::extPath('mailform').'lib/layout/interface/interface.tx_mailform_I_layout.php');

/**
 * tx_mailform_style
 *
 */
class tx_mailform_style extends tx_mailform_parent implements tx_mailform_I_content,
																			tx_mailform_I_layout {
	
	private $content = '';
	
	public function getContent() {
		return $this->content;
	}
	
	public function setContent($content) {
		$this->content = $content;
	}
	
	public function getRenderedElement() {
		$this->appendString("<style");
		$this->appendString($this->appendString($this->attributes->getAttributes()));
		$this->appendString(">\n");
		$this->appendString($this->getContent());
		$this->appendString("\n</style>\n");
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/layout/css/class.tx_mailform_style.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/layout/css/class.tx_mailform_style.php']);
}
?>