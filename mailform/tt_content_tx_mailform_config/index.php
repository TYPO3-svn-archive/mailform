<?php
session_start();
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
unset($MCONF);
require ('conf.php');
require ($BACK_PATH.'init.php');
require ($BACK_PATH.'template.php');

require_once(t3lib_extMgm::extPath('mailform')."formTypesModel/class.tx_mailform_form.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/class.tx_mailform_funcLib.php");
require_once(t3lib_extMgm::extPath('mailform')."hooks/class.tx_mailform_BE_Handler.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/class.tx_mailform_urlHandler.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/class.tx_mailform_WizardWrapper.php");
require_once(t3lib_extMgm::extPath('mailform')."tt_content_tx_mailform_config/model/class.tx_mailform_wizard.php");
require_once(t3lib_extMgm::extPath('mailform')."tt_content_tx_mailform_config/singletons/class.tx_mailform_saveState.php");
require_once(t3lib_extMgm::extPath('mailform')."tt_content_tx_mailform_config/singletons/class.tx_mailform_formHandler.php");

// Language File init

require_once (PATH_t3lib.'class.t3lib_scbase.php');
require_once (PATH_t3lib.'class.t3lib_tcemain.php');
require_once (PATH_t3lib.'class.t3lib_tsparser.php');
require_once (PATH_t3lib.'class.t3lib_page.php'); 
require_once (PATH_t3lib.'class.t3lib_tstemplate.php'); 
require_once (PATH_t3lib.'class.t3lib_tsparser_ext.php'); 
 
/**
* mailform module tx_mailform_tt_content_tx_mailform_configwiz
*
* @author        Sebastian Winterhalder <sw@internetgalerie.ch>
*
*/
class tx_mailform_tt_content_tx_mailform_configwiz extends t3lib_SCbase {

	// Internal, dynamic:
	public $doc;
	// Document template object
	public $content;
	public $conf;
	private $wizDisplay;
	public $newUniquefieldName = "uniquefieldname";

	/**
	 * Class Constructor
	 *
	 */
	public function __construct() {
		global $BE_USER, $LANG, $BACK_PATH, $BE_Handler,$CONF;
		parent::init();

		$P = t3lib_div::_GP('P');
		$this->loadTS($P['pid']);
		$CONF = $this->conf;
		
		$BE_Handler = tx_mailform_BE_Handler::getInstance($P['uid']);

		// Initialize GP Vars
		$this->initMenu();

		// Load Configuration
		$tmfH = tx_mailform_formHandler::getInstance();
		$this->handleCloseDokFunctions();

		$this->createWizard('extended');
		$this->init();
	}

	/**
	 * Initialization the class
	 *
	 * @return	void
	 
	public function init() {
      global $BACK_PATH, $LANG, $BE_USER, $BE_Handler;

      $this->doc = t3lib_div::makeInstance('bigDoc');
			$this->doc->backPath = $BACK_PATH;

		if(isset($_GET['newField'])) {
			$urlString = '&amp;edtField='.$BE_Handler->getFormHandler()->getUniqueFieldName()."#fieldWizard";
		}

		if(isset($_GET['edtField'])) {
			$urlString = '&amp;edtField='.$_GET['edtField']."#fieldWizard";
		}

		$this->doc->form='<form action="'.$BE_Handler->getUrlHandler()->getCurrentUrl(tx_mailform_wizard::getVars()).$urlString.'" method="POST" name="editform">';

		// JavaScript
		$this->doc->JScode = '
			<script language="javascript" type="text/javascript">
				script_ended = 0;
				function jumpToUrl(URL)	{
					document.location = URL;
				}
				function tx_mailform_promptChangeType() {
					if(confirm("'.htmlspecialchars($LANG->getLL('form_confirm_to_save_and_change')).'")) {
						document.qForm.submit();
						return true;
					}
					else {
						return false;
					}
				}
			</script>
			<script type="text/javascript" src="standard_js.js"><!-- by sw --></script>
			<script type="text/javascript" src="overlib/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>
			<script type="text/javascript" src="javascript.js"><!-- copied in typo3 source --></script>
		';
		$this->doc->postCode='
			<script language="javascript" type="text/javascript">
				script_ended = 1;
				if (top.fsMod) top.fsMod.recentIds["web"] = 0;
			</script>
		';

		/** Add CSS Styles 
		$cssResource = fopen("wizardStyle.css", "r");
		$css = fread($cssResource, filesize("wizardStyle.css"));
		$this->doc->inDocStyles .= $css;
		fclose($cssResource);
		/** END Add css Styles 

		
    	$this->content = $this->doc->startPage($LANG->getLL('forms_title'));
    	
    	/*
		$this->content.= $this->doc->header($LANG->getLL('forms_title'));
		$this->content.= ($this->hasChanged() && !((isset($_POST['savedok_x']) || isset($_POST['saveandclosedok_x'])))) ? '<p><table cellpadding="0" cellspacing="1" border="0"><tr><td><img src="../gfx/database.gif"></td><td><font style="color:#F00;">'.$LANG->getLL('info_form_has_changed').'</font></td></tr></table></p>' : '';

		if(isset($_GET['delReferences'])) {
			$saveState = tx_mailform_saveState::getInstance();
			$saveState->setChanged(true);
			$BE_Handler->getFormHandler()->deleteUnusedForms();
		}

		if($BE_Handler->getFormHandler()->isAFormUnreferenced()) {
			$this->content .= '<p><table cellpadding="0" cellspacing="1" border="0"><tr>
									<td><img src="../gfx/reference_warning.gif"></td>
									<td><font style="color:#4e2e0b;">'.$LANG->getLL('fWiz_warning_unreferenced_fields').'</font>
										<b>(<a href="'.$BE_Handler->getUrlHandler()->getCurrentUrl(array_merge(tx_mailform_wizard::getVars(), array('delReferences'))).'&amp;delReferences=1" onclick="return confirm(\''.$LANG->getLL('really_delete_unrefforms').'\');">'.$LANG->getLL('delete_unrefforms').'</a>)</b></td>
										</tr></table></p>';
		}

	 	// Initialize Page
		$EXT['ext_key'] = "mailform";
		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$BE_Handler->Pageinfo = t3lib_BEfunc::readPageAccess($this->id,$BE_Handler->Perms_clause);

		//$this->content.= $this->doc->sectionBegin();
		//$this->content .= '<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>';
		/*$this->content.= '
		<table width="100%"><tr><td>'.$this->getFormHeader().'</td>
								<td align="right">'.$this->getMenu().'</td></tr></table>';
		//$this->content.= $this->doc->sectionEnd();

		//$this->setModuleContent();
		
		$this->content.= '<table width="100%"><tr><td><hr size="100%">'.$this->getFormHeader().'</td></tr></table>';
	
		// Make save field
		$content = '<div id="typo3-docheader" style="padding:0;">
						<div id="typo3-docheader-row1">asdf</div>
						<div id="typo3-docheader-row2">asdf</div>
					</div>';
		$content .= '<div id="tx_mailform_docbody">'.$this->content.'</div>';
		
		//'.$this->content.'
		$this->content = $content;
	}
*/
	

	/**
	 * Initialization the class
	 *
	 * @return	void
	 */
	public function init() {
		global $BACK_PATH, $LANG, $EXT, $BE_Handler,$TYPO3_GLOBAL_VARS;

		$EXT['ext_key'] = "mailform";
		
		if(isset($_GET['newField'])) {
		$urlString = '&amp;edtField='.$BE_Handler->getFormHandler()->getUniqueFieldName()."#fieldWizard";
		}
		if(isset($_GET['edtField'])) {
			$urlString = '&amp;edtField='.$_GET['edtField']."#fieldWizard";
		}
		if(isset($_GET['delReferences'])) {
			$saveState = tx_mailform_saveState::getInstance();
			$saveState->setChanged(true);
			$BE_Handler->getFormHandler()->deleteUnusedForms();
		}
		
		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$BE_Handler->Pageinfo = t3lib_BEfunc::readPageAccess($this->id,$BE_Handler->Perms_clause);
		
		// Create an instance of the document template object
		t3lib_div::debug($TYPO3_GLOBAL_VARS);
		
		$this->doc = $GLOBALS['TBE_TEMPLATE'];
		$this->doc->backPath = $BACK_PATH;
		$this->doc->setModuleTemplate('templates/alt_doc.html');
		$this->doc->docType = 'xhtml_trans';
		$this->doc->form='<form action="'.$BE_Handler->getUrlHandler()->getCurrentUrl(tx_mailform_wizard::getVars()).$urlString.'" method="POST" name="editform">';
		
			// Build the <body> for the module
		$this->content = $this->doc->startPage('TYPO3 Edit Document');
		
		$changed = ($this->hasChanged() && !((isset($_POST['savedok_x']) || isset($_POST['saveandclosedok_x'])))) ? '<p><table cellpadding="0" cellspacing="1" border="0"><tr><td><img src="../gfx/database.gif"></td><td><font style="color:#F00;">'.$LANG->getLL('info_form_has_changed').'</font></td></tr></table></p>' : '';
		
		if($BE_Handler->getFormHandler()->isAFormUnreferenced()) {
			$deleteRef .= '<p><table cellpadding="0" cellspacing="1" border="0"><tr>
									<td><img src="../gfx/reference_warning.gif"></td>
									<td><font style="color:#4e2e0b;">'.$LANG->getLL('fWiz_warning_unreferenced_fields').'</font>
										<b>(<a href="'.$BE_Handler->getUrlHandler()->getCurrentUrl(array_merge(tx_mailform_wizard::getVars(), array('delReferences'))).'&amp;delReferences=1" onclick="return confirm(\''.$LANG->getLL('really_delete_unrefforms').'\');">'.$LANG->getLL('delete_unrefforms').'</a>)</b></td>
										</tr></table></p>';
		} else {$deleteRef = '';}
		
		
		$header = '
		<table cellpadding="1" cellspacing="0" width="100%" border="0">
			<tr>
				<td><b>'.$LANG->getLL('forms_title').'</b></td>
				<td align="right">'.$deleteRef.'</td>
				<td align="right">'.$changed.'</td>
			</tr>
		</table>
		';
		
		$this->content.= '
			<div id="typo3-docheader" style="padding:0px;">
				<div id="typo3-docheader-row1">'.$this->getFormHeader().'</div>
				<div style="border-bottom: 2px solid #595d66;height:27px;line-height:normal;">'.$header.'</div>
			</div>
			<div id="typo3-docbody">';

		$this->setModuleContent();
		
		$this->content .= '</div>';
		$this->content.= $this->doc->endPage();
		
		// JavaScript
		$this->doc->JScode = '
			<script language="javascript" type="text/javascript">
				script_ended = 0;
				function jumpToUrl(URL)	{
					document.location = URL;
				}
				function tx_mailform_promptChangeType() {
					if(confirm("'.htmlspecialchars($LANG->getLL('form_confirm_to_save_and_change')).'")) {
						document.qForm.submit();
						return true;
					}
					else {
						return false;
					}
				}
			</script>
			<script type="text/javascript" src="standard_js.js"><!-- by sw --></script>
			<script type="text/javascript" src="overlib/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>
			<script type="text/javascript" src="javascript.js"><!-- copied in typo3 source --></script>
		';
		$this->doc->postCode='
			<script language="javascript" type="text/javascript">
				script_ended = 1;
				if (top.fsMod) top.fsMod.recentIds["web"] = 0;
			</script>
			<style type="text/css">
			 body {
			 	margin: 0px;
			 }
			</style>
		';

		/** Add CSS Styles */
		$cssResource = fopen("wizardStyle.css", "r");
		$css = fread($cssResource, filesize("wizardStyle.css"));
		$this->doc->inDocStyles .= $css;
		fclose($cssResource);
		
		
		$this->content = $this->doc->insertStylesAndJS($this->content);
		
		
		/*
    	$this->content = $this->doc->startPage($LANG->getLL('forms_title'));
    	
    	/*
		$this->content.= $this->doc->header($LANG->getLL('forms_title'));
		$this->content.= ($this->hasChanged() && !((isset($_POST['savedok_x']) || isset($_POST['saveandclosedok_x'])))) ? '<p><table cellpadding="0" cellspacing="1" border="0"><tr><td><img src="../gfx/database.gif"></td><td><font style="color:#F00;">'.$LANG->getLL('info_form_has_changed').'</font></td></tr></table></p>' : '';

		

		

	 	// Initialize Page
		

		$this->content.= $this->doc->sectionBegin();
		$this->content .= '<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>';
		$this->content.= '
		<table width="100%"><tr><td>'.$this->getFormHeader().'</td>
								<td align="right">'.$this->getMenu().'</td></tr></table>';
		$this->content.= $this->doc->sectionEnd();

		$this->setModuleContent();
		
		// Make save field
		$content = '';
		$content .= '<'.$this->content.'';
		
		//'.$this->content.'
		$this->content = $content;
*/
	}
	
	/**
	* Adds items to the ->MOD_MENU array. Used for the function menu selector.
	*
	* @return	void
	*/
	public function getMenu()	{
		global $BE_Handler;
		require_once(t3lib_extMgm::extPath('mailform').'lib/layout/form/class.tx_mailform_select.php');
		$selectBox = new tx_mailform_select();
		$selectBox->setOnchange('jumpToUrl(\'index.php'.$BE_Handler->getUrlHandler()->getCurrentUrl(tx_mailform_wizard::getVars(),true,true).'&amp;SET[function]=\'+this.options[this.selectedIndex].value,this)');
		$arrKeys = $this->MOD_MENU['function'];
		$gp = t3lib_div::_GP('SET');
		$_SESSION['tx_mailform']['SET'] = $gp;
		foreach($arrKeys as $key => $value) {
			$opt = new tx_mailform_option();
			$opt->setValue($key);
			$opt->setContent($value);

			if($_SESSION['tx_mailform']['SET']['function'] == $key) {
				$opt->setSelected(true);
			} else $opt->setSelected(false);
			$selectBox->addContent($opt);
		}
		parent::menuConfig();

		return $selectBox->getElementRendered();
	}

	/**
	 * Init Menu
	 *
	 */
	private function initMenu() {
		global $BE_Handler;

		$this->MOD_MENU = Array (
			'function' => $BE_Handler->getWizardWrapper()->getMenuFunctionArray()
		);
		parent::menuConfig();
	}

	/**
	 * SetModuleContent();
	 *
	 */
	private function setModuleContent() {
  	global $LANG, $BE_Handler;
		$this->content .= $this->doc->divider('2');

		switch($this->MOD_SETTINGS['function'])	{
			case 0:
				$this->content .= $this->wizardMenu();
			break;
			case 1:
					$this->content .= $this->doc->section($LANG->getLL('functions_'.$wizardType.'Editor'), $this->wizDisplay->getElementRendered(), 0, 1);
				break;
			default:
				if(!$BE_Handler->getWizardWrapper()->getContent($this->MOD_SETTINGS['function']))
					$this->content .= $this->wizardMenu();
				else
					$this->content .= $BE_Handler->getWizardWrapper()->getContent($this->MOD_SETTINGS['function']);
			break;
		}
	}

	/**
	 * Add the Wizard Menu
	 *
	 */
	private function wizardMenu() {
		global $LANG, $BE_Handler;
		$str = "<b>".$LANG->getLL('functions_menu')."</b>";
		return $str.$BE_Handler->getWizardWrapper()->getWizardBigIcons();
	}

	/**
	 * createWizard();
	 *
	 * @param String $wizardType
	 */
	private function createWizard($wizardType) {
		require_once(t3lib_extMgm::extPath('mailform')."tt_content_tx_mailform_config/model/class.tx_mailform_".$wizardType."Wiz.php");
		$this->wizDisplay = t3lib_div::makeInstance('tx_mailform_'.$wizardType.'Wiz');
		$this->wizDisplay->init();
	}

	/**
	 * Returns if the document has changed (true if unsaved values)
	 *
	 * @return Boolean
	 */
	public function hasChanged() {
		$tmsS = tx_mailform_saveState::getInstance();
		return $tmsS->hasChanged();
	}

	/**
	 * Public level: Set Data changed
	 *
	 */
	public function setChanged($boolean=true) {
		$tmsS = tx_mailform_saveState::getInstance();
		$tmsS->setChanged($boolean);
	}

	/**
	 * saveDocument();
	 *
	 */
	private function saveDocument() {
	  global $BE_Handler;
		if(empty($this->wizDisplay))
			$this->createWizard('extended');

		$tmcD = tx_mailform_configData::getInstance();
		$bodyText = $tmcD->getCompleteXML();

		// Make TCEmain object:
		$tce = t3lib_div::makeInstance('t3lib_TCEmain');
		$tce->stripslashes_values = 0;
		// Put content into the data array:
		$data = array();
		$data[$BE_Handler->P['table']][$BE_Handler->P['uid']][$BE_Handler->P['field']] = $bodyText;
		// Perform the update:
		$tce->start($data, array());
		$tce->process_datamap();

		$wizardWrapper = tx_mailform_WizardWrapper::getInstance();
		$wizardWrapper->saveWizards();

		$this->setChanged(false);
	}


	/**
	 * HandleCloseDokFunctions();
	 *
	 */
	private function handleCloseDokFunctions() {
	  global $BE_Handler;
		// If a save button has been pressed, then save the new field content:
		if ($_POST['savedok_x'] || $_POST['saveandclosedok_x']) {
			$this->saveDocument();
			// If the save/close button was pressed, then redirect the screen:
			if ($_POST['saveandclosedok_x']) {
				header('Location: '.t3lib_div::locationHeaderUrl($BE_Handler->P['returnUrl']));
				exit;
			}
		}

		if($_POST['formResetChanges_x']) {
			$saveState = tx_mailform_saveState::getInstance();
			$saveState->setChanged(false);
			
			$BE_Handler->getConfigData()->unsetConfigInSession();
		}
	}

	/**
	 * Generate save buttons
	 *
	 * @return	string  FormHeader
	 */
	private function getFormHeader() {
		global $LANG, $BE_Handler;

		$saveState = tx_mailform_saveState::getInstance();
		if($saveState->hasChanged())
			$last_version = '<input type="image" class="c-inputButton" name="formResetChanges"'.t3lib_iconWorks::skinImg('../gfx/undo.gif', '').' title="'.$LANG->getLL('fWiz_reset_to_last_version', 1).'" onclick="confirm(\'Really?\')">'."\n";
		else
			$last_version = '';
		return '
			<!-- Save Buttons start -->
			<!--<div id="c-saveButtonPanel">-->'
			.'<input type="image" class="c-inputButton" name="savedok"'.t3lib_iconWorks::skinImg($this->doc->backPath, 'gfx/savedok.gif', '').' title="'.$LANG->sL('LLL:EXT:lang/locallang_core.php:rm.saveDoc', 1).'" />'."\n"
			.'<input type="image" class="c-inputButton" name="saveandclosedok"'.t3lib_iconWorks::skinImg($this->doc->backPath, 'gfx/saveandclosedok.gif', '').' title="'.$LANG->sL('LLL:EXT:lang/locallang_core.php:rm.saveCloseDoc', 1).'" />'."\n"
			.'<a href="#" onclick="'.htmlspecialchars('jumpToUrl(unescape(\''.rawurlencode($BE_Handler->P['returnUrl']).'\')); return false;').'">'. '<img'.t3lib_iconWorks::skinImg($this->doc->backPath, 'gfx/closedok.gif', 'width="21" height="16"').' class="c-inputButton" title="'.$LANG->sL('LLL:EXT:lang/locallang_core.php:rm.closeDoc', 1).'" alt="" />'. '</a>'."\n"
			.'<input type="image" class="c-inputButton" name="_refresh"'.t3lib_iconWorks::skinImg($this->doc->backPath, 'gfx/refresh_n.gif', '').' title="'.$LANG->getLL('forms_refresh', 1).'" />'."\n"
			.$last_version
			.'<!-- </div> -->
			<!-- Save Buttons end -->
			';
	}

	/**
	* Outputting the accumulated content to screen
	*
	* @return	void
	*/
	function printContent() {
		global $PERF_Mon;
		$this->content .= $this->doc->endPage();
		$this->content = $this->doc->insertStylesAndJS($this->content);
		
		echo $this->content;
	}
	
	function loadTS($pageUid) {
       $sysPageObj = t3lib_div::makeInstance('t3lib_pageSelect'); 
       $rootLine = $sysPageObj->getRootLine($pageUid); 
       $TSObj = t3lib_div::makeInstance('t3lib_tsparser_ext'); 
       $TSObj->tt_track = 0; 
       $TSObj->init(); 
       $TSObj->runThroughTemplates($rootLine); 
       $TSObj->generateConfig(); 
       $this->conf = $TSObj->setup['plugin.']['tx_mailform_pi1.']; 
	}
	
	function __toString() {
		return 'configWiz: Index.php';
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/tt_content_tx_mailform_config/index.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/tt_content_tx_mailform_config/index.php']);
}

// Execute the Class
// Make instance:
$SOBE = t3lib_div::makeInstance('tx_mailform_tt_content_tx_mailform_configwiz');
$SOBE->printContent();

?>