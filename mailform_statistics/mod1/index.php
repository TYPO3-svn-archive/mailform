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


	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require_once('conf.php');
require_once($BACK_PATH.'init.php');
require_once($BACK_PATH.'template.php');
require_once(t3lib_extMgm::extPath('mailform').'/lib/class.tx_mailform_urlHandler.php');
require_once(t3lib_extMgm::extPath('mailform').'/lib/class.tx_mailform_funcLib.php');
require_once(t3lib_extMgm::extPath('mailform')."lib/database/class.tx_mailform_db_ttContentRow.php");
$LANG->includeLLFile('EXT:mailform_statistics/mod1/locallang.xml');
require_once(PATH_t3lib.'class.t3lib_scbase.php');

$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.

error_reporting(E_ALL ^ E_NOTICE);

/**
 * Module 'Email Statistics' for the 'mailform' extension.
 *
 * @author	Sebastian Winterhalder <sw@internetgalerie.ch>
 * @package	TYPO3
 * @subpackage	mailform_statistic
 */
class  tx_mailformstatistics_module1 extends t3lib_SCbase {

	var $pageinfo;

	protected $urlVars = array('download' => 'mod1Download');
	/**
	 * Initializes the Module
	 * @return	void
	 */
	function init()	{
	global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

	parent::init();

	$this->pageId = intval(t3lib_div::_GP("id"));
	}

	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	void
	 */
	function menuConfig()	{
		global $LANG;
		$this->MOD_MENU = Array (
			'function' => Array (
			'1' => $LANG->getLL('function_form_overview'),
			'2' => $LANG->getLL('function_mail_detail'),
			'4' => $LANG->getLL('function_form_detail'),
			)
		);
		parent::menuConfig();
	}

	/**
	 * Main function of the module. Write the content to $this->content
	 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	 *
	 * @return	[type]		...
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS, $EXT;
		$EXT['ext_key'] = "mailform_statistics";
		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;

		if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))	{
			// Draw the header.
			$this->doc = t3lib_div::makeInstance('mediumDoc');
			$this->doc->backPath = $BACK_PATH;
			$this->doc->form='<form action="" method="POST">';

			// JavaScript
			$this->doc->JScode = '
				<script language="javascript" type="text/javascript">
				script_ended = 0;
				function jumpToUrl(URL)	{
				document.location = URL;
				}
				</script>
			';
			$this->doc->postCode='
				<script language="javascript" type="text/javascript">
				script_ended = 1;
				if (top.fsMod) top.fsMod.recentIds["web"] = 0;
				</script>
			';

			/** Add CSS Styles */
			$cssResource = fopen("mod1.css", "r");
			$css = fread($cssResource, filesize("mod1.css"));
			$this->doc->inDocStyles .= $css;
			fclose($cssResource);
			/** END Add css Styles */

			//$headerSection = $this->doc->getHeader('pages',$this->pageinfo,$this->pageinfo['_thePath']).'<br />'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.path').': '.t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'],50);

			$this->content.=$this->doc->startPage($LANG->getLL('title'));
			$this->content.=$this->doc->header($LANG->getLL('title'));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
			$this->content.=$this->doc->divider(5);

			$this->moduleContent();

			// ShortCut
			if ($BE_USER->mayMakeShortcut())	{
				$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
			}

			$this->content.=$this->doc->spacer(10);
		} else {
			// If no access or if ID == zero
			$this->doc = t3lib_div::makeInstance('mediumDoc');
			$this->doc->backPath = $BACK_PATH;

			$this->content.=$this->doc->startPage($LANG->getLL('title'));
			$this->content.=$this->doc->header($LANG->getLL('title'));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->spacer(10);
		}
	}

	/**
	* Prints out the module HTML
	*
	* @return	void
	*/
	function printContent()	{
		$this->content.=$this->doc->endPage();
		// If mod1Download is set, dont output standard content
		if(isset($_GET[$this->urlVars['download']])) {
		$this->createFile();
		} else {
		echo $this->content;
		}
	}

	/**
	 * Generates the module content
	 *
	 * @return	void
	 */
	function moduleContent() {
	$command = t3lib_div::GPvar('command');
		switch((string)$this->MOD_SETTINGS['function'])	{
			case 1:
				// Overview
				$this->content.= $this->getOverview();
			break;
			case 2:
				$this->content .= $this->getDetailMailView();
				// Mail detail view
			break;
			case 3:
				$this->content.=$this->doc->section('Message #3:',$content,0,1);
			break;
			case 4:
				// Formular Statistic
				$this->content.= $this->getSingleView();
			break;
		}
	}



	/**
	 * getDetailMailView
	 *
	 * @return Mixed
	 */
	function getDetailMailView() {
		require_once(t3lib_extMgm::extPath('mailform_statistics')."mod1/display/class.tx_mailformstatistics_mailDetail.php");
		$mailDetail = t3lib_div::makeInstance('tx_mailformstatistics_mailDetail');

		if(isset($_GET['elmId']))
			return $mailDetail->getContent();
		else
			return $this->getOverview();
	}


	/** Create Overview of Email Statistics */
	function getOverview() {
		global $LANG;

		$string.=$this->doc->section($LANG->getLL("form_overview"), $cont);
		require_once("display/class.tx_mailformstatistics_disp.php");

		$sql = "SELECT tt_content.uid, tt_content.header, tt_content.tstamp, tt_content.pi_flexform FROM tt_content
			WHERE CType = 'list' AND list_type = 'mailform_pi1'";

		$result = $GLOBALS['TYPO3_DB']->sql_query($sql);
		$title = array(  $LANG->getLL('mod1_formular_title') => "",
						$LANG->getLL('mod1_formular_subject') => "",
						$LANG->getLL('mod1_formular_changeDate') => "",
						$LANG->getLL('mod1_formular_options') => ' align="right"'
					);

		$cont = array(); // Init variable
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
			$openHref = "?id=0&amp;SET[function]=4&amp;elmId=".$row['uid'];
			/** Define Header */
			if($row['header'] == "")
				$row['header'] = '['.$LANG->getLL('mod1_formular_notitle').']';
			$row['header'] = '<a href="'.$openHref.'" class="modLink">'.$row['header'].'</a>';
			/** End Define Header */

			/** Define Options */
			$options = '<a href="'.$openHref.'"><img src="gfx/zoom.png" border="0" alt="'.$LANG->getLL("mod1_formular_zoom").'"></a>';
			/** End Define Options */

			/** Prepare Flexform Data */
			$flexiData = t3lib_div::xml2array($row['pi_flexform']);
			/** End Prepare Flexform Data */

			$subject = "";



			if(is_array($flexiData['data']) ) {
				$subjectText = $flexiData['data']['admin_mailconfig']['lDEF']['subject']['vDEF'];
				$subject = '<a href="'.$openHref.'" class="modLink">'.tx_mailform_funcLib::shortenText($subjectText, 40).'</a>';
				$subject = ($subjectText != '') ? $subject : $LANG->getLL("mod1_formular_noSubject");
			}
			else
			$subject = $LANG->getLL("mod1_formular_noSubject");

			$cont[] = array($row['header'], $subject, date("d.m.Y", $row['tstamp']), $options);
		}

		$string.= tx_mailformstatistics_disp::createTable($cont, $title);

		return $string;
	}

	function getSingleView() {
		global $LANG;
		// If the formular element id is not set return the overview
		if(empty($_GET['elmId']))
			return $this->getOverview();

		$urlC = t3lib_div::makeInstance('tx_mailform_urlHandler');

		// Define URL Variables at one place
		$url['excel']     = $urlC->getCurrentUrl(array('SET[function]'))."&SET[function]=4&".$this->urlVars['download']."=excel";
		$url['mailView']  = $urlC->getCurrentUrl(array('SET[function]'))."&SET[function]=2";

		$svHeader = '<table cellpadding="1" cellspacing="0" width="100%"><tr>
			<td width="10"><a href="'.$url['mailView'].'" class="modLink"><img src="gfx/email.png" alt="'.$LANG->getLL('mod_formular_viewEmails').'" border="0" /></a></td>
			<td><a href="'.$url['mailView'].'" class="modLink">'.$LANG->getLL('mod_formular_viewEmails').'</a></td>
			<td width="10"><a href="'.$url['excel'].'" class="modLink"><img src="gfx/page_excel.png" alt="" border=""></a></td>
			<td><a href="'.$url['excel'].'" class="modLink">'.$LANG->getLL('mod_formular_downloadExcel').'</a>
			<td width="*"></td>
			</tr></table>';
		$string .= '<table cellpadding="2" cellspacing="0" width="100%"><tr><td class="naviElement">'.$this->doc->section('', $svHeader, 1,1,1,1).'</td></tr></table>';

		// Prepare Data
		$sql = "SELECT * FROM tx_mailformstatistics_mails WHERE formid = ".$_GET['elmId'];
		$result = $GLOBALS['TYPO3_DB']->sql_query($sql);
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
			$time[] = $row;
		}

		// Initialize arrays
		$mailcount = $daycount = $datecount = $hourcount = array();
		for($x = 0; $x < sizeof($time); $x ++) {
			$d = "date_".date("M",$time[$x]['tstamp']);
			$month = $LANG->getLL(strtolower($d))." ".date("Y", $time[$x]['tstamp']);

		if(empty($mailcount[$month]))
			$mailcount[$month] = 1;
		else
			$mailcount[$month]++;

		$day = $LANG->getLL("day_".strtolower(date("D", $time[$x]['tstamp'])));
		if(empty($daycount[$day]))
			$daycount[$day] = 1;
		else
			$daycount[$day]++;

		$date = date("d.m.Y", $time[$x]['tstamp']);
		if(empty($datecount[$date]))
			$datecount[$date] = 1;
		else
			$datecount[$date]++;

		$hour = date("H", $time[$x]['tstamp']).":00 - ".date("H", $time[$x]['tstamp']+3600).":00";
		if(empty($hourcount[$hour]))
			$hourcount[$hour] = 1;
		else
			$hourcount[$hour]++;
		}

		/** Overview Month */
		$string .= $this->doc->section($LANG->getLL('mod1_overview_month'), $this->createStats($mailcount), 1,0,0,1);

		/** Overview Week */
		$string .= $this->doc->section($LANG->getLL('mod1_overview_week'), $this->createStats($daycount), 1,0,0,1);

		/** Overview Days */
		$string .= $this->doc->section($LANG->getLL('mod1_overview_day'), $this->createStats($datecount), 1,0,0,1);

		/** Overview Hours */
		$string .= $this->doc->section($LANG->getLL('mod1_overview_hour'), $this->createStats($hourcount), 1,0,0,1);

		return $string;
	}


	public function createFile() {
		global $LANG;

		$flex = t3lib_div::xml2array(tx_mailform_db_ttContentRow::getInstance()->getFlexformXML());

		$aSub = $flex['data']['admin_mailconfig']['lDEF']['subject']['vDEF'];
		$bSub = $flex['data']['s_mailconfig']['lDEF']['subject']['vDEF'];

		$subject = "";
		if($bSub != $aSub) {
			if(strlen($aSub) > 0)
				$subject = $aSub;
			if(strlen($aSub) > 0 && strlen($bSub) > 0)
				$subject .= " - ";
			if(strlen($bSub) > 0)
				$subject .= $bSub;
		} else {
			if(strlen($aSub) > 0)
				$subject = $aSub;
			if(strlen($bSub) > 0)
				$subject = $bSub;
		}

		require_once(t3lib_extMgm::extPath("mailform")."/lib/class.tx_mailform_excelHandler.php");
		$excelHandler = t3lib_div::makeInstance("tx_mailform_excelHandler");
		$excelHandler->main();

		$pdfObj = new PHPExcel();
		$pdfObj->setActiveSheetIndex(0);
		$pdfObj->getActiveSheet()->setCellValue('A1', $subject);
		$pdfObj->getActiveSheet()->setBreak('A1', PHPExcel_Worksheet::BREAK_ROW);

		/**
		* -------------------
		*/

		/* Style Title */
		$pdfObj->getActiveSheet()->getRowDimension('1')->setRowHeight('60');

		// Background Color
		$BackgroundColor = new PHPExcel_Style_Color();
		$BackgroundColor->setRGB("4D70AA");

		// Font Color
		$FontColor = new PHPExcel_Style_Color();
		$FontColor->setRGB("C5D6F2");

		// Set background
		$pdfObj->getActiveSheet()->getStyle("A1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$pdfObj->getActiveSheet()->getStyle("A1")->getFill()->setStartColor($BackgroundColor);

		// Design Font
		$pdfObj->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
		$pdfObj->getActiveSheet()->getStyle("A1")->getFont()->setColor($FontColor);
		$pdfObj->getActiveSheet()->getStyle("A1")->getFont()->setSize("14");

		// Design Border
		$pdfObj->getActiveSheet()->getStyle("A1")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
		$pdfObj->getActiveSheet()->getStyle("A1")->getBorders()->getBottom()->setColor($FontColor);

		/**
		* -------------------
		*/

		/* Style Description Row */
		$pdfObj->getActiveSheet()->getRowDimension('2')->setRowHeight('20');

		// Background Color
		$BackgroundColor = new PHPExcel_Style_Color();
		$BackgroundColor->setRGB("CAD8EF");

		// Font Color
		$FontColor = new PHPExcel_Style_Color();
		$FontColor->setRGB("364154");

		// Set background
		$pdfObj->getActiveSheet()->getStyle("A2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$pdfObj->getActiveSheet()->getStyle("A2")->getFill()->setStartColor($BackgroundColor);

		// Design Font
		$pdfObj->getActiveSheet()->getStyle("A2")->getFont()->setBold(true);
		$pdfObj->getActiveSheet()->getStyle("A2")->getFont()->setColor($FontColor);
		$pdfObj->getActiveSheet()->getStyle("A2")->getFont()->setSize("10");

		// Design Border
		$pdfObj->getActiveSheet()->getStyle("A2")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$pdfObj->getActiveSheet()->getStyle("A2")->getBorders()->getBottom()->setColor($FontColor);

		// Duplicate the first colums
		$pdfObj->getActiveSheet()->duplicateStyle( $pdfObj->getActiveSheet()->getStyle("A2"), "B2:E2");
		/* End Style Description Row */

		/**
		* -------------------
		*/

		/** Setting spreadsheets active sheet */
		$pdfObj->getProperties()->setCreator("TYPO3 ext: mailform - Sebastian Winterhalder");
		$pdfObj->getProperties()->setLastModifiedBy("TYPO3 ext: mailform - Sebastian Winterhalder");
		$pdfObj->getProperties()->setCategory($LANG->getLL('excel_category'));
		$pdfObj->getProperties()->setTitle($subject);

		/** Field Generator */
		require_once(t3lib_extMgm::extPath('mailform')."lib/class.tx_mailform_fieldGenerator.php");
		
		$fieldGenerator = t3lib_div::makeInstance('tx_mailform_fieldGenerator');
		$fieldGenerator->generateContentRows();
		// Title
		$titleRow = $fieldGenerator->getTitleRow();

		// Set the Date additional to the first column
		$fieldCount = 2;

		$pdfObj->getActiveSheet()->setCellValue("A".$fieldCount, $LANG->getLL('excel_datum'));

		foreach($titleRow as $field) {
			if($field->getForm()->isFieldInStats()) {
				$col = tx_mailform_excelHandler::getCharWithColID($fieldCount);
				$pdfObj->getActiveSheet()->setCellValue($col."2", $field->getForm()->getLabel());
				$fieldCount++;
			}
		}

		// Content
		$mailRows = $fieldGenerator->getMailRows();
		$rowCount = 3;
		foreach($mailRows as $row) {
			$BackgroundColor = new PHPExcel_Style_Color();
			if(($rowCount % 2) == 0)
				$BackgroundColor->setRGB("DDE7F7");
			else
				$BackgroundColor->setRGB("F0F6FF");
			// Set the Date additional to the first column
			$fieldCount = 2;

			$db_mailrow_assoc_arr = tx_mailform_db_mailsOfForm::getInstance()->getRows();

			$pdfObj->getActiveSheet()->setCellValue("A".$rowCount, date("d.m.Y - H:i",$db_mailrow_assoc_arr[$rowCount - 3]['tstamp']));
			$pdfObj->getActiveSheet()->getStyle("A".$rowCount)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$pdfObj->getActiveSheet()->getStyle("A".$rowCount)->getFill()->setStartColor($BackgroundColor);

			foreach($row as $field) {
			if($field->getForm()->isFieldInStats()) {
			$col = tx_mailform_excelHandler::getCharWithColID($fieldCount);
			$fieldName = $col.$rowCount;

			// Set background
			$pdfObj->getActiveSheet()->getStyle($fieldName)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			$pdfObj->getActiveSheet()->getStyle($fieldName)->getFill()->setStartColor($BackgroundColor);

			// End Field Style

			/// UTF-8 Konvertieren
			$xfieldContent = $field->getForm()->getCurrentContent();
			$xfieldContent = utf8_decode($xfieldContent);

			$pdfObj->getActiveSheet()->setCellValue($fieldName, $xfieldContent);

			$fieldCount++;
			}
			}
			$rowCount++;
		}

		if($rowCount > 0) {
			$charCol = tx_mailform_excelHandler::getCharWithColID($fieldCount-1);

			$pdfObj->getActiveSheet()->duplicateStyle( $pdfObj->getActiveSheet()->getStyle("A1"), "B1:".$charCol."1");
			$pdfObj->getActiveSheet()->duplicateStyle( $pdfObj->getActiveSheet()->getStyle("A2"), "B2:".$charCol."2");
		}

		$excelHandler->writeAndSend($pdfObj, 5);
	}

	/**
	 *  $arrsStat = array("Title" => "12", "Title 2" => "1"); --> array(label => value);
	 *
	 * @param $arrStat
	 *
	 */
	function createStats($arrStat) {
		global $LANG;
		if(sizeof($arrStat) <= 0) {
			return $LANG->getLL("mod1_noStats");
		}

		$string = '<table cellpadding="0" border="0" cellspacing="0" border="0">';

		// Calculate maximas and minimas
		foreach($arrStat as $value) {
			$min = (intval($value) < $min) ? intval($value) : $min;
			$max = (intval($value) > $max) ? intval($value) : $max;
			$tot = intval($value) + $tot;
		}
		// Clear max to level zero

		$max = $max - $min;

		foreach($arrStat as $title => $value) {
			$value = $value - $min;
			$pValue = ($value / $tot) * 100;
			$mpValue = 100 - $pValue;
			$string .= '
			<tr>
			 <td width="100">'.$title.'</td>
			 <td>
			<table border="0" width="150" cellpadding="0" cellspacing="0">
			 <tr><td width="'.$pValue.'%"><img src="gfx/verlauf/v1.gif" width="100%" height="10"></td>
			 <td width="'.$mpValue.'%">'.$value.'</td>
			</tr>
			</table>
			</td></tr>';
		}

		$string .= '</table>';
		return $string;
	}

	function choosePageContent() {
		global $LANG;
		$this->content .= $this->doc->section($LANG->getLL('choosePage'), '', 1, 1, 1, 1);
	}

	function drawContent($contentArray) {
		assert(is_array($contentArray));
		foreach($contentArray as $part) {
			if(is_array($part)) {
				$this->content .= $this->doc->section($part[0], $part[1], 0, 1);
				$this->content .= $this->doc->spacer(10);
			}
			else
				$this->content .= $this->doc->divider(intval($part));
		}
	}

	function drawHeader($header){
		$this->content .= '<h2>'.htmlspecialchars($header).'</h2>';
	}

	function drawMessage($msg) {
		assert(is_array($msg));
		$this->content .= $this->doc->divider(15);
		if($msg[0]=='F')
			$this->content .= $this->doc->section('&nbsp;<span style="color:red">Fehler:</span>', $msg[1], 0, 0, 3, 1);
		else
			$this->content .= $this->doc->section('&nbsp;Meldung:', $msg[1], 0, 0, 1, 1);
		$this->content .= $this->doc->divider(15);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform_statistics/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform_statistics/mod1/index.php']);
}

// Make instance:
$SOBE = t3lib_div::makeInstance('tx_mailformstatistics_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>
