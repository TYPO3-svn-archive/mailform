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

class tx_mailformtmpl_display implements tx_mailform_displayInterface {

	private static $display;
	private $urlHandler;

	public static function getInstance() {
		if(empty(self::$display)) {
			self::$display = new tx_mailformtmpl_display();
		}
		return self::$display;
	}

	private function __construct() {
		$this->urlHandler = new tx_mailform_urlHandler();
	}

	public function getWizardImage() {
		global $LANG;
		return '<img src="'.tx_mailform_parentWizard::getRelativePath('mailform_tmpl').'wiz_icon.png" alt="'.$LANG->getLL('').'" border="0">';
	}

	public function getContent() {
		$case = t3lib_div::_GP('extmft');
		switch($case) {
			case 0:
				$this->replaceAction();
				$content = $this->getAllTemplates();
				break;
			case 1:
				$content = $this->formSaveHandler();
				break;
			case 2:
				$content = $this->settingsController();
				break;
			default:
			
			break;
		}
		return $this->extNavigation().$content;
	}

	private function replaceAction() {
		$replAction = t3lib_div::_GP('mftmpl_repl');

		/*
		$urlHandler = new tx_mailform_urlHandler();
		$url = 'P[params]='.$_GET['P']['params']."&".'P[exampleImg]='.$_GET['P']['exampleImg'].'&P[table]='.$_GET['P']['table'].'&P[uid]='.$_GET['P']['uid'].'&P[pid]='.$_GET['P']['pid'].'&P[field]='.$_GET['P']['md5ID'].'&P[field]='.$_GET['P']['returnUrl']."SET[function]=1";
		$targetUrl = 'http://'.t3lib_div::getIndpEnv('HTTP_HOST')."/".t3lib_extMgm::siteRelPath('mailform')."tt_content_tx_mailform_config/index.php?".$url;
		*/

		if(isset($replAction)) {
			$loader = tx_mailformtmpl_loader::getInstance();
			$loader->replaceTemplateWith($replAction);

		}
		$history_post = t3lib_div::_GP('mftmpl_history');
		if(!empty($history_post)) {
			$loader = tx_mailformtmpl_loader::getInstance();
			$history = tx_mailformtmpl_history::getInstance();
			$histObjects = $history->getHistoryObjects();
			foreach($histObjects as $hisObj) {
				if($hisObj->getUid() == $history_post) {
					$loader->replaceTemplateWithHistory($hisObj);
				}
			}
		}
	}

	private function getAllTemplates() {
		global $LANG;
		$loader = tx_mailformtmpl_loader::getInstance();
		$array = $loader->getTemplates();

		$table = new tx_mailform_table();
		$table->setWidth("100%");

		$row = new tx_mailform_tr();
		$c1 = new tx_mailform_td();
		$c1->addStyle('font-weight:bold;');
		$c1->setContent($LANG->getLL('xml_form_author'));
		$row->addTd($c1);

		$c2 = new tx_mailform_td();
		$c2->addStyle('font-weight:bold;');
		$c2->setContent($LANG->getLL('xml_form_title'));
		$row->addTd($c2);

		$PI = new tx_mailform_processInput();
		$c3 = new tx_mailform_td();
		$c3->addStyle('font-weight:bold;');
		$c3->setContent($LANG->getLL('xml_form_description'));
		$row->addTd($c3);

		$c4 = new tx_mailform_td();
		$c4->addStyle('font-weight:bold;');
		$c4->setContent($LANG->getLL('xml_form_date'));
		$row->addTd($c4);

		$clink = new tx_mailform_td();
		$clink->addStyle('font-weight:bold;');
		$clink->setContent('');
		$row->addTd($clink);
		$table->addRow($row);

		foreach($array as $obj) {
			$table->addRow($obj->getDisplay());
		}

		$historyElements = tx_mailformtmpl_history::getInstance();
		return $table->getElementRendered().$historyElements->getDisplay()->getElementRendered();
	}

	private function formSaveHandler() {
		global $LANG;
		if(isset($_POST)) {
			$arr = array(
				'xml_save_author' => t3lib_div::_GP('xml_save_author'),
				'xml_save_title' => t3lib_div::_GP('xml_save_title'),
				'xml_save_desc' => t3lib_div::_GP('xml_save_desc'),
				'xml_save_date' => time(),
				'xml_form_saveFolder' => t3lib_div::_GP('xml_form_saveFolder'),
			);

			$flag = true;
			if(strlen($arr['xml_save_author']) <= 0) {
				$err['xml_save_author'] = $LANG->getLL('xml_save_error_author');
				$flag = false;
			}
			if(strlen($arr['xml_save_title']) <= 0) {
				$err['xml_save_title'] = $LANG->getLL('xml_save_error_title');
				$flag = false;
			}
			if(strlen($arr['xml_save_desc']) <= 0) {
				$err['xml_save_desc'] = $LANG->getLL('xml_save_error_desc');
				$flag = false;
			}
			if($flag) {
				$tmplObj = new tx_mailformtmpl_templateObj();
				$tmplObj->setTitle($arr['xml_save_title']);
				$tmplObj->setDescription($arr['xml_save_desc']);
				$tmplObj->setAuthor($arr['xml_save_author']);
				$tmplObj->setDate($arr['xml_save_date']);

				try {
          $templatePaths = tx_mailformtmpl_loader::getInstance()->getTemplatePath();
					$tmplObj->setDir($templatePaths[$arr['xml_form_saveFolder']]);
				} catch (Exception $e) {
				  try {
				    	$tmplObj->setDir(tx_mailform_funcLib::parseExtPath(tx_mailformtmpl_settings::$defautlArray['XML_STANDARD']));
						} catch (Exception $e) {
							$tmplObj->setDir(t3lib_extMgm::extPath('mailform_tmpl')."/xml_templates");
						}
          }
				
				$loader = tx_mailformtmpl_loader::getInstance();
				$tmplObj->setUid($loader->highest_uid+1);

				$cfData = tx_mailform_configData::getInstance();

				$tmplObj->setConfigXML(t3lib_div::xml2array($cfData->getCompleteXML()));
				$tmplObj->saveXMLinFile();
				return '<div style="padding: 5px; font-weight: bold; font-size: 10px; color: #F00;">'.$LANG->getLL('xml_saved')."</div>".$this->getAllTemplates();
			} else
				return $this->saveTemplateForm($arr, $err);
		} else {
			// new
			return $this->saveTemplateForm();
		}
	}

	private function settingsController() {
		global $LANG;
		$settings = tx_mailformtmpl_settings::getInstance();


		if(!empty($_POST)) {
			if($_POST['settings_history_enable'] == 1) {
				tx_mailformtmpl_settings::saveVariable('SAVE_HISTORY', 1);
			} else {
				tx_mailformtmpl_settings::saveVariable('SAVE_HISTORY', 0);
			}

			if($_POST['settings_history_savedtimes'] > 0) {
				tx_mailformtmpl_settings::saveVariable('NR_SAVINGS', $_POST['settings_history_savedtimes']);
			} else {
				$error['settings_history_savedtimes'] = $LANG->getLL('error_settings_history_savedtimes');
			}

			tx_mailformtmpl_settings::saveVariable('XML_STANDARD', $_POST['xml_standard_template_root']);
			tx_mailformtmpl_settings::saveVariable('XML_USER', $_POST['xml_user_template_root']);

			if(isset($_POST['reset'])) {
				$arr = tx_mailformtmpl_settings::$defautlArray;

				foreach($arr as $key => $value) {
					tx_mailformtmpl_settings::saveVariable($key, $value);
				}
			}
		}
		
		

		$values['settings_history_enable'] = tx_mailformtmpl_settings::getVariable('SAVE_HISTORY');
		$values['settings_history_savedtimes'] = tx_mailformtmpl_settings::getVariable('NR_SAVINGS');
		$values['xml_standard_template_root'] = tx_mailformtmpl_settings::getVariable('XML_STANDARD');
		$values['xml_user_template_root'] = tx_mailformtmpl_settings::getVariable('XML_USER');

		try {
			$tmpPath = tx_mailform_funcLib::parseExtPath($values['xml_standard_template_root']);
		} catch (Exception $e) {
			$error['xml_standard_template_root'] .= $LANG->getLL('extension_does_not_exist')."<br>";
		}
		if(!file_exists($tmpPath)) {
			$error['xml_standard_template_root'] .= $LANG->getLL('error_path');
		} else {
			if(!is_dir($tmpPath))
				$error['xml_standard_template_root'] .= $LANG->getLL('error_dir');
		}

		try {
			$tmpPath = tx_mailform_funcLib::parseExtPath($values['xml_user_template_root']);
		} catch (Exception $e) {
			$error['xml_user_template_root'] .= $LANG->getLL('extension_does_not_exist')."<br>";
		}
		if(!file_exists($tmpPath)) {
			$error['xml_user_template_root'] .= $LANG->getLL('error_path');
		} else {
			if(!is_dir($tmpPath))
				$error['xml_user_template_root'] .= $LANG->getLL('error_dir');
		}

		return $this->settings($values, $error);
	}

	private function saveTemplateForm($fieldArray = array(), $errorArray = array()) {
		global $LANG;
		$form = new tx_mailform_htmlform();
		$urlHandler = new tx_mailform_urlHandler();
		$form->setAction($urlHandler->getCurrentUrl(tx_mailformtmpl::$critical_getVars));
		$form->setMethod('post');

		$table = new tx_mailform_table();
		$row1 = new tx_mailform_tr();
		$col1 = new tx_mailform_td();
		$col1->addStyle('font-weight: bold;');
		$col1->addStyle('width:150px;');
		$col2 = new tx_mailform_td();

		$inputName = new tx_mailform_input();
		$inputName->setName('xml_save_title');
		$inputName->setValue($fieldArray['xml_save_title']);
		$col1->setContent($form->getStartElement().$LANG->getLL('xml_form_title'));
		$row1->addTd($col1);
		$col2->setContent($inputName->getElementRendered().$errorArray['xml_save_title']);
		$row1->addTd($col2);
		$table->addRow($row1);

		$row1 = new tx_mailform_tr();
		$inputName = new tx_mailform_input();
		$inputName->setName('xml_save_author');
		$inputName->setValue($fieldArray['xml_save_author']);
		$col1 = new tx_mailform_td();
		$col1->addStyle('font-weight: bold');
		$col1->setContent($LANG->getLL('xml_form_author'));
		$row1->addTd($col1);
		$col2 = new tx_mailform_td();
		$col2->setContent($inputName->getElementRendered().$errorArray['xml_save_author']);
		$row1->addTd($col2);
		$table->addRow($row1);

		$row1 = new tx_mailform_tr();
		$inputName = new tx_mailform_textarea();
		$inputName->setName('xml_save_desc');
		$inputName->setContent($fieldArray['xml_save_desc']);
		$col1 = new tx_mailform_td();
		$col1->addStyle('font-weight: bold');
		$col1->setContent($LANG->getLL('xml_form_desc'));
		$row1->addTd($col1);
		$col2 = new tx_mailform_td();
		$col2->setContent($inputName->getElementRendered().$errorArray['xml_save_desc']);
		$row1->addTd($col2);
		$table->addRow($row1);

		$row1 = new tx_mailform_tr();
		$col1 = new tx_mailform_td();
		$col1->addStyle('font-weight: bold');
		$col1->setContent($LANG->getLL('xml_form_date'));
		$row1->addTd($col1);
		$col2 = new tx_mailform_td();
		$col2->setContent(date("d.m.Y"));
		$row1->addTd($col2);
		$table->addRow($row1);

		$row1 = new tx_mailform_tr();
		$col1 = new tx_mailform_td();
		$col2 = new tx_mailform_td();
		$col1->addStyle('font-weight: bold');
		$col1->setContent($LANG->getLL('xml_form_saveFolder'));
		$input = new tx_mailform_select();
		$input->setName('xml_form_saveFolder');
		$input->setSize(1);
		$pathArr = tx_mailformtmpl_loader::getInstance()->getTemplatePath();
		if(!is_array($pathArr))
			$pathArr = array();

		foreach($pathArr as $key => $path) {
			$option = new tx_mailform_option();
			$option->setValue($key);
			$option->setContent($path);
			if($key == $fieldArray['xml_save_author']) {
				$option->setSelected();
			}
			$input->addContent($option);
		}
		$col2->setContent($input->getElementRendered());
    $row1->addTd($col1);
		$row1->addTd($col2);
    $table->addRow($row1);


		$inputType = new tx_mailform_input();
		$inputType->setType('submit');
		$inputType->setValue($LANG->getLL('xml_form_send'));
		$row1 = new tx_mailform_tr();
		$col1 = new tx_mailform_td();
		$row1->addTd($col1);
		$col2 = new tx_mailform_td();
		$col2->setContent($inputType->getElementRendered().$form->getEndElement());
		$row1->addTd($col2);
		$table->addRow($row1);

		return $table->getElementRendered();
	}

	private function settings($fieldArray, $errorArray) {
		global $LANG;
		$form = new tx_mailform_htmlform();
		$urlHandler = new tx_mailform_urlHandler();
		$form->setAction($urlHandler->getCurrentUrl(tx_mailformtmpl::$critical_getVars));
		$form->setMethod('post');

		$table = new tx_mailform_table();
		$table->addStyle("border-top: 1px solid #8fb5d6; margin-top: 2px; width: 100%;");
		$table->setCellspacing(0);
		$table->setCellpadding(4);

		$row1 = new tx_mailform_tr();
		$col1 = new tx_mailform_td();
		$col1->addStyle('font-weight: bold;');
		$col1->addStyle('width:150px;');
		$col2 = new tx_mailform_td();

		$checkboxSettings = new tx_mailform_checkbox();
		$checkboxSettings->setName('settings_history_enable');
		$checkboxSettings->setValue(1);
		$checkboxSettings->setChecked($fieldArray['settings_history_enable'] == 1);
		$col1->setContent($form->getStartElement().$LANG->getLL('settings_history_enable'));
		$row1->addTd($col1);
		$col2->setContent($checkboxSettings->getElementRendered().$errorArray['settings_history_enable']);
		$row1->addTd($col2);
		$table->addRow($row1);

		$row1 = new tx_mailform_tr();
		$inputName = new tx_mailform_input();
		$inputName->setName('settings_history_savedtimes');
		$inputName->setValue($fieldArray['settings_history_savedtimes']);
		$col1 = new tx_mailform_td();
		$col1->addStyle("border-bottom: 1px solid #8fb5d6;");
		$col1->addStyle('font-weight: bold');
		$col1->setContent($LANG->getLL('settings_history_savedtimes'));
		$row1->addTd($col1);
		$col2 = new tx_mailform_td();
		$col2->addStyle("border-bottom: 1px solid #8fb5d6;");
		$col2->setContent($inputName->getElementRendered().$errorArray['settings_history_savedtimes']);
		$row1->addTd($col2);
		$table->addRow($row1);


		$row1 = new tx_mailform_tr();
		$inputName = new tx_mailform_input();
		$inputName->setName('xml_standard_template_root');
		$inputName->setValue($fieldArray['xml_standard_template_root']);
		$inputName->setSize(50);
		$col1 = new tx_mailform_td();
		$col1->addStyle("border-bottom: 0px none #8fb5d6;");
		$col1->addStyle('font-weight: bold');
		$col1->setContent($LANG->getLL('xml_standard_template_root'));
		$row1->addTd($col1);
		$col2 = new tx_mailform_td();
		$col2->addStyle("border-bottom: 0px none #8fb5d6;");
		$col2->setContent($this->parseContent($inputName->getElementRendered(),$errorArray['xml_standard_template_root']));
		$row1->addTd($col2);
		$table->addRow($row1);

		$row1 = new tx_mailform_tr();
		$inputName = new tx_mailform_input();
		$inputName->setName('xml_user_template_root');
		$inputName->setValue($fieldArray['xml_user_template_root']);
		$inputName->setSize(50);
		$col1 = new tx_mailform_td();
		$col1->addStyle("border-bottom: 1px solid #8fb5d6;");
		$col1->addStyle('font-weight: bold');
		$col1->setContent($LANG->getLL('xml_user_template_root'));
		$row1->addTd($col1);
		$col2 = new tx_mailform_td();
		$col2->addStyle("border-bottom: 1px solid #8fb5d6;");
		$col2->setContent($this->parseContent($inputName->getElementRendered(),$errorArray['xml_user_template_root']));
		$row1->addTd($col2);
		$table->addRow($row1);

		$inputType = new tx_mailform_input();
		$inputType->setType('submit');
		$inputType->setValue($LANG->getLL('xml_form_send'));
		$it1 = new tx_mailform_input();
		$it1->setType('reset');
		$it1->setValue($LANG->getLL('xml_form_reset'));
		$it2 = new tx_mailform_input();
		$it2->setName('reset');
		$it2->setValue($LANG->getLL('xml_form_default'));
		$it2->setType('submit');
		$row1 = new tx_mailform_tr();
		$col1 = new tx_mailform_td();
		$row1->addTd($col1);
		$col2 = new tx_mailform_td();
		$col2->setContent($inputType->getElementRendered().$it1->getElementRendered().$it2->getElementRendered().$form->getEndElement());
		$row1->addTd($col2);
		$table->addRow($row1);

		return $table->getElementRendered();
	}
	
	/**
	 * parseContent($content, $error)
	 *
	 *
	 * @return String
	 */
	private function parseContent($content, $error) {
		return '<div style="float:left;">'.$content.'</div><div style="float:left; color: #F00; font-weight: bold;">'.$error.'</div>';
	}

	/**
	 * extNavigation();
	 *
	 * @return String;
	 */
	public function extNavigation() {
		global $LANG;
		$urlHandler = new tx_mailform_urlHandler();
		$t = '
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td width="100%" style="padding-top: 2px; border-top:1px solid #80acff; border-left: 0px none #000; border-right: 0px none #000; border-bottom: 1px solid #80acff; background-color:#e5eeff;">
					<a href="'.$urlHandler->getCurrentUrl(tx_mailformtmpl::$critical_getVars, true, true).'&extmft=1"><img src="'.tx_mailform_parentWizard::getRelativePath('mailform_tmpl').'/gfx/save_template.png" title="'.$LANG->getLL('save_current_configuration').'" alt="'.$LANG->getLL('save_current_configuration').'"></a>
					<a href="'.$urlHandler->getCurrentUrl(tx_mailformtmpl::$critical_getVars, true, true).'&extmft=0"><img src="'.tx_mailform_parentWizard::getRelativePath('mailform_tmpl').'/gfx/lupe.png" title="'.$LANG->getLL('list_all_templates').'" alt="'.$LANG->getLL('list_all_templates').'"></a>
					<a href="'.$urlHandler->getCurrentUrl(tx_mailformtmpl::$critical_getVars, true, true).'&extmft=2"><img src="'.tx_mailform_parentWizard::getRelativePath('mailform_tmpl').'/gfx/settings.png" title="'.$LANG->getLL('settings').'" alt="'.$LANG->getLL('settings').'"></a>
					</td>
				</tr>
			</table>
		';
		return $t;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform_tmpl/lib/class.tx_mailformtmpl_display.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform_tmpl/lib/class.tx_mailformtmpl_display.php']);
}

?>
