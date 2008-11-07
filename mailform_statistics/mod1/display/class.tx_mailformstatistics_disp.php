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
 *
 *
 *
 */   
class  tx_mailformstatistics_disp {
			
	/**
	 * Initializes the Display
	 * @return	void
	 */
	function init()	{
		
	}
	
	/**
	 * The Parameter must have following form
	 * 
	 * @param array $contentAssocArray array(array('content line 1 field 1', 'content line 1 field 2), array('content line 2 field 1', 'content line 2 field 2'))
	 * @param array $titleArray array('title1' => 'AttriButes', 'title2' => 'AttriButes');
	 *
	 */	
	public static function createTable($contentAssocArray, $titleArray) {
    
    $result [] = '
    <script type="text/javascript">
      function colorizeOnmouse(Line, Size) {
        var IdName = \'line_\' + Line + \'_\' + Size;

        alert(\'test\');
      }
      function colorizeOnmouseout(Line, Size) {
        var IdName = \'line_\' + Line + \'_\' + Size;

        alert(\'test2\');
      }
    </script>
    <table width="100%" border="0" cellpadding="0" cellspacing="0">';
    
    /** Create title of table */
    $line  = '
      <tr>
    ';
    foreach($titleArray as $title => $lineVal) {
          $line .= "<td".$lineVal."><b>".$title."</b></td>";
        }
        
    $line .= '
      </tr>';
    /** End of title of table */
    $result [] = $line;
    
    /** Create Content of table */
    $ak = array_keys($titleArray);
    foreach($contentAssocArray as $key => $val) {
      $line = '
		    <tr class="courseListElement">
		    		';
        for($x = 0; $x < sizeof($val); $x ++) {
          if(strlen($val[$x]) <= 0)
            $val[$x] = "&nbsp;";
          $line .= '<td id="line_'.$key.'_'.$x.'" class="courseListElement"'.$titleArray[$ak[$x]].'>'.$val[$x].'</td>';
        }

        $line .= '
		    </tr>
		    <tr>
		    	<td style="height: 1px;" colspan="'.sizeof($titleArray).'"></td>
		    </tr>';
		    
		    $result [] = $line;
    }
    /** Content fo table created */
    
    $result [] = '</table>';
    
    
    return implode($result);
  }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform_statistics/mod1/display/class.tx_mailformstatistics_disp.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform_statistics/mod1/display/class.tx_mailformstatistics_disp.php']);
}


?>
