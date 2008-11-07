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


require_once(t3lib_extMgm::extPath("mailform")."lib/class.tx_mailform_urlHandler.php");
require_once(t3lib_extMgm::extPath("mailform_statistics")."mod1/display/class.tx_mailformstatistics_disp.php");
require_once(t3lib_extMgm::extPath("mailform")."lib/database/class.tx_mailform_db_mailsOfForm.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/class.tx_mailform_fieldGenerator.php");
		
/**
 *
 * Backend Display Class
 *
 */   
class  tx_mailformstatistics_mailDetail {

	private $fields;
	
	public function __construct() {
		
	}
  
	public function getContent() {
		if(isset($_GET['det'])) {
			switch($_GET['det']) {
				case 'del':
					$result = $this->getDelete();
				break;
				case 'edt':
					$result = $this->getEdit();
				break;
				case 'dis':
					$result = $this->getDetailView();
				break;
				default:
					$result = $this->getOverview();
			}
		} elseif(isset($_GET['elStats'])) {
			$result = "Stats";
		} else {
			$result = $this->getOverview();
		}
		
		return $this->getHeader().$result;
	}
  
  private function getOverview() {
    global $LANG;
    $table = "";
    tx_mailform_db_mailsOfForm::getInstance()->sort('uid', false);
    $tmp = tx_mailform_db_mailsOfForm::getInstance()->getRows();
    
    $urlHandler = new tx_mailform_urlHandler();
    foreach($tmp as $key => $value) {
      $optionElements = '<table cellpadding="1" cellspacing="0" border="0"><tr>';
      $optionElements .= '<td><a href="?'.$urlHandler->getCurrentUrl(array('det', 'del')).'&det=del&item='.$value['mailid'].'" onclick="return confirmDelete()"><img src="gfx/email_delete.png" border="0" alt=""></a></td>';
     // $optionElements .= '<td><a href="?'.$urlHandler->getCurrentUrl(array('det', 'del')).'&det=edt&item='.$value['mailid'].'"><img src="gfx/email_edit.png" border="0" alt=""></a></td>';
      $optionElements .= '<td><a href="?'.$urlHandler->getCurrentUrl(array('det', 'del')).'&det=dis&item='.$value['mailid'].'"><img src="gfx/zoom.png" border="0" alt=""></a></td>';
      $optionElements .= '<tr></table>';
      
      $arr[] = array(date("d.m.Y H:i", $value['tstamp']), $value['recipient'], $value['subject'], $optionElements);
    }
    
    if(sizeof($arr) == 0)
      $arr[] = array('&nbsp;',$LANG->getLL('mod1_noEmails'),'&nbsp;','&nbsp;');
    
    $titleArray = array($LANG->getLL('mod1_datum') => ' width="110px"',
                        $LANG->getLL('mod1_from') => ' ',
                        $LANG->getLL('mod1_formular_subject') => ' ',
                        '' => ' ');
    
    $table .= tx_mailformstatistics_disp::createTable($arr, $titleArray);

    return $table;
  }
  
  private function getEdit() {
    global $LANG;
    require_once(t3lib_extMgm::extPath('mailform')."lib/database/class.tx_mailform_db_ttContentRow.php");
    require_once(t3lib_extMgm::extPath('mailform')."lib/database/class.tx_mailform_db_mailFieldContent.php");
    require_once(t3lib_extMgm::extPath('mailform')."formTypesModel/class.tx_mailform_form.php");

    $res = tx_mailform_db_ttContentRow::getInstance()->getRows();
    $fieldArr = t3lib_div::xml2array($res[0]['tx_mailform_config']);

    /* Create all Fields */
    foreach($fieldArr['mailform_forms'] as $page) {
    	foreach($page as $row) {
    		$fieldObj = tx_mailform_funcLib::createField($row['uName'], $fieldArr['mailform_forms']);
      		$fieldObj->getForm()->setupCurrentContent($field);
    		$objField[] = $fieldObj;
    	}
    }
    
    require_once(t3lib_extMgm::extPath('mailform')."formTypesModel/class.tx_mailform_formOutputGenerator.php");
    $formGenerator = t3lib_div::makeInstance("tx_mailform_formOutputGenerator");
    for($x = 0; $x < count($objField); $x++) {
      $formGenerator->addField($objField[$x]->getForm()->getFEHtml());
    }
    
    
    $fields = tx_mailform_db_mailFieldContent::getInstance()->getRows();
    
    $objectArray = array();
    foreach($fields as $field) {
      $fieldObj = tx_mailform_funcLib::createField($field['ufid'], $fieldArr['mailform_forms']);
      $fieldObj->getForm()->setupCurrentContent($field);
      $objectArray[] = $fieldObj;
    }
    
    require_once(t3lib_extMgm::extPath('mailform')."formTypesModel/class.tx_mailform_formOutputGenerator.php");
    $formGenerator = t3lib_div::makeInstance("tx_mailform_formOutputGenerator");
    for($x = 0; $x < count($objectArray); $x++) {
      $formGenerator->addField($objectArray[$x]->getForm()->getFEHtml());
    }
    
    $form = '
      <table cellpadding="0" cellspacing="0" border="0" width="100%">
      <tr><td><form action="" method="post" name=""></td></tr>
      <tr><td>
    '.$formGenerator->getTable().'
      </td></tr>
      <tr>
        <td>
          <hr width="100%">
          <input type="submit" name="updateForm" value="'.$LANG->getLL('mod1_updateForm').'">
        </td>
      </tr>
      </form>
    ';
    
    return $form;
  }
  
  private function getDelete() {
    $sql = "DELETE FROM tx_mailformstatistics_stats WHERE mailid = '".$_GET['item']."'";
    $GLOBALS['TYPO3_DB']->sql_query($sql);
    $sql = "DELETE FROM tx_mailformstatistics_mails WHERE mailid = '".$_GET['item']."'";
    $GLOBALS['TYPO3_DB']->sql_query($sql);

    return $this->getOverview();
  }
  
  private function loadData() {
  	require_once(t3lib_extMgm::extPath('mailform')."hooks/class.tx_mailform_BE_Handler.php");
  	$BE_Handler = tx_mailform_BE_Handler::getInstance(t3lib_div::_GP("elmId"));
	$this->fields = $BE_Handler->getFormHandler()->getForms();
	
  	$sql = "SELECT * FROM  tx_mailformstatistics_stats WHERE mailid = '".t3lib_div::_GP("item")."'";
  	$res = $GLOBALS['TYPO3_DB']->sql_query($sql);
  	
  	while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
  		foreach($this->fields as $fields) {
  			foreach($fields as $field)  {
	  			if($field->getForm()->getUFID() == $row['ufid']) {
	  				
	  				$arr = array($field->getForm()->getFormType() => $row[$row['data_type']]);
	  				$key = array_keys($arr);
	  				$xmlArr = t3lib_div::xml2array($arr[$key[0]]);
	  				
	  				
	  				if(!is_array($xmlArr)) {
	  					$result = $row[$row['data_type']];
	  				} else {
	  					$result = $xmlArr['name'];
	  				}
	  				$field->getForm()->setupCurrentContent($row[$row['data_type']]);
	  			}
  			}
  		}
  	}
  }
  
  private function getDetailView() {
  	$this->loadData();	

  	$fieldGenerator = t3lib_div::makeInstance('tx_mailform_fieldGenerator');
	$fieldGenerator->generateContentRows();
  	$mailRows = $fieldGenerator->generateContentRow(array(t3lib_div::_GP("item")));
  	
    $result = '<table width="100%" cellpadding="2" cellspacing="0" border="0">';
    foreach($this->fields as $page) {
    	foreach($page as $field) {
	      $result .= '
	        <tr>
	          <td><b>'.$field->getForm()->getLabel().'</b></td>
	          <td>'.$field->getForm()->getCurrentContent().'</td>
	        </tr>
	      ';
    	}
    }
    
    return $result;
  }
  
  private function getHeader() {
    global $LANG;
    $urlHandler = new tx_mailform_urlHandler();
    $url = "?".$urlHandler->getCurrentUrl(array('elStats', 'det', 'item', 'SET[function]'))."&SET[function]=4";
    $svHeader = '
                      <script type="text/javascript" lang="javascript">
                        function confirmDelete() {
                          return confirm(\''.$LANG->getLL('mod1_confirmDelete').'\');
                        }
                      </script>
                      <table cellpadding="1" cellspacing="0" width="100%" class="naviElement"><tr>
                        <td width="10"><a href="'.$url.'" class="modLink"><img src="gfx/diagramCook.gif" alt="'.$LANG->getLL('mod1_emailStats').'" border="0" /></a></td>
                        <td><a href="'.$url.'" class="modLink">'.$LANG->getLL('mod1_emailStats').'</a></td>
                        <td width="*"></td>
                      </tr></table>';
    return $svHeader;
  }
  
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform_statistics/mod1/display/class.tx_mailformstatistics_mailDetail.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform_statistics/mod1/display/class.tx_mailformstatistics_mailDetail.php']);
}


?>
