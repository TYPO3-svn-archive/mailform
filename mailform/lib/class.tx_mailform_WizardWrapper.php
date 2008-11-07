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

class tx_mailform_WizardWrapper {

	private static $wrapper;
	private $wizards = array();
	private $urlHandler;
	private $count_static_menu_entries = 2;

	public static function getInstance() {
		if(empty(self::$display)) {
			self::$wrapper = new tx_mailform_WizardWrapper();
		}
		return self::$wrapper;
	}

	/**
	 * Add here all wizards to be loaded
	 * Do not change anything anywhere else
	 * The wizards must be implementations of classes in folder wizardInterface
	 */
	private function __construct() {
	  $addons = $this->getAddonsExtKey();
		foreach($addons as $extKey) {
			$this->loadWizard($extKey);
		}

		$this->urlHandler = new tx_mailform_urlHandler();
	}

	private function getAddonsExtKey() {
		$BE_Handler = tx_mailform_BE_Handler::getInstance();
		$arr = $BE_Handler->getTSConf();
		$result = array();
		foreach($arr as $key => $variable) {
			$int =  preg_match('(enable_addon_+)', $key, $treffer);
			if($int == 1 && $variable == 1) {
				$result[] = str_replace($treffer[0], '', $key);
			}
		}
		return $result;
	}

	private function loadWizard($ext_key) {
		global $LANG;
		if(t3lib_extMgm::isLoaded($ext_key)) {
			$shortened_name = str_replace("_", "", $ext_key);
			if(!file_exists(t3lib_extMgm::extPath($ext_key)."class.tx_".$ext_key.".php"))
				$clName = $shortened_name;
			else
				$clName = $ext_key;
			require_once(t3lib_extMgm::extPath($ext_key)."class.tx_".$clName.".php");
			$class_name = 'tx_'.$clName;
			$LANG->includeLLFile('EXT:'.$ext_key.'/locallang.xml');

			$instance = t3lib_div::makeInstance($class_name);
			$this->addWizard($ext_key, true, $instance);
		}
	}

	private function addWizard($ext_key, $bool_loaded, $objWiz) {

		$this->wizards[] = array($ext_key, $bool_loaded, $objWiz);
		/*
		$exist = array(false, 0);
		foreach($this->wizards as $key => $row) {
			if($row[0] = $ext_key)
				$exist = array(true, $key);
		}

		if($exist[0]) {
			$this->wizards[$exist[1]] = array($ext_key, $bool_loaded, $objWiz);
			t3lib_div::debug($exist);
		}
		else {
			$this->wizards[] = array($ext_key, $bool_loaded, $objWiz);
		}
		*/
	}

	public function getMenuFunctionArray() {
		global $LANG;

		$menuItems = Array (
			'1' => $LANG->getLL('functions_extendedEditor'),
			'0' => $LANG->getLL('functions_menu'),
		);


		$x=$this->count_static_menu_entries;

		foreach($this->wizards as $wizard) {
			$menuItems[$x] = $LANG->getLL('functions_wiz_'.$wizard[0]);
			$x++;
		}
		return $menuItems;
	}

	public function getWizardBigIcons() {
		$x = 1;
		$y = 0;

		$str = '<tr>
				<td align="center"><a href="'.$this->urlHandler->getCurrentUrl(array('SET')).'&SET[function]=1"><img src="../gfx/extended_wiz.gif" alt="" border="0"></a></td>';

		foreach($this->wizards as $key => $wizard) {

			$key = $key + $this->count_static_menu_entries;
			if($x == 0)
				$str .= "
				<tr>";

			$str .= '
				<td align="center"><a href="'.$this->urlHandler->getCurrentUrl(array('SET')).'&SET[function]='.$key.'">'.$wizard[2]->getDisplay()->getWizardImage().'</a></td>';


			$x++;
			if($x > 8) {
				$str .= "
				</tr>";
			 $y ++;
			 $x = 0;
			}
		}

		return '
		<table align="center" width="200">
			'.$str.'
		</table>
		';
	}

	public function getContent($modSettings) {
		global $LANG;

		for($x = 0; $x < sizeof($this->wizards); $x++) {
				if($modSettings == ($x+$this->count_static_menu_entries))
					return $this->wizards[$x][2]->getDisplay()->getContent();
		}
		return false;
	}

	public function saveWizards() {
		for($x = 0; $x < sizeof($this->wizards); $x++) {
			$this->wizards[$x][2]->saveWizard();
		}
	}
}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/class.tx_mailform_WizardWrapper.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/class.tx_mailform_WizardWrapper.php']);
}

?>