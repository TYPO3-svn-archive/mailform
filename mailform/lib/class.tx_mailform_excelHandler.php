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

/**
* mailform module
*
* @author       Sebastian Winterhalder <sw@internetgalerie.ch>
* 
*/
class tx_mailform_excelHandler  {
  
  private $libRoot = "";

  /**
   *  Constructor
   */           
  public function __construct() {
    $this->excel = "";
    $this->libRoot = t3lib_extMgm::extPath('mailform')."lib/phpexcel/";
    $this->includeAllFiles();
  }
  
  /**
   * Constructor for typo3 environment
   */     
  public function main() {
    self::__construct();
  }
  
  /**
   * Write excel
   * 
   * @param $version PossibleValues: 5 | 7      
   *
   */
  public function writeAndSend($objPHPExcel, $i) {
	global $BACK_PATH;
  switch ($i) {
    case 5:
      //require_once(t3lib_extMgm::extPath('mailform')."lib/phpexcel/PHPExcel/Writer/Excel5.php");
      $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
    break;
    case 7:
      //require_once(t3lib_extMgm::extPath('mailform')."lib/phpexcel/PHPExcel/Writer/Excel2007.php");
      $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    break;
    default: 
      //require_once(t3lib_extMgm::extPath('mailform')."lib/phpexcel/PHPExcel/Writer/Excel2007.php");
      $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
    break;
  }
    
  	$tempDir =  $BACK_PATH.'../fileadmin/_temp_/';
  	
  	if(!file_exists($tempDir) || true) {
  		$tempDir = t3lib_extMgm::extPath('mailform')."/temp/";
  		if(!file_exists($tempDir)) {
  			mkdir($tempDir);
  			$fRes = @fopen($tempDir."index.html", "rw");
  			@fwrite($fRes, "<h1>Access Denied</h1>");
  			@fclose($fRes);
  		}
  	}
  
    require_once(t3lib_extMgm::extPath('mailform')."lib/class.tx_mailform_fileHandler.php");
    $FH = new tx_mailform_fileHandler();
    $filename = $FH->getUniqueFilename("excel_stats.xls", $tempDir);
    $objWriter->save($filename);
    
    $FH->sendFileToUser($filename, time()."_excelStatistic.xls");
  }
  
  /**
   * Inlcude all PHPExcel files
   *
   */        
  private function includeAllFiles() {
    require_once(t3lib_extMgm::extPath("phpexcel_library")."sv1/class.tx_phpexcellibrary_sv1.php");
    $PHPExcelSV = t3lib_div::makeInstance("tx_phpexcellibrary_sv1");
    $PHPExcelSV->init();
  }

  /**
   * $param int $ColID 0 - unlimited
   *
   */        
  public static function getCharWithColID($ColID) {
    $array = array('"ERROR"', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    
    $charStr = "";
    while($ColID >= 1) {
      $index = (($ColID % 26) == 0) ? 26 : (($ColID % 26));
      
      $charStr = $array[$index].$charStr;
      
      if($index == 26)
        $ColID --;
      
      $ColID = $ColID / 26;
    }
    
    return $charStr;
  }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/class.tx_mailform_excelHandler.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/class.tx_mailform_excelHandler.php']);
}

?>