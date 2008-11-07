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
*/


#Class URL CONTROL

#  Documentation of function get_url_array()
#  The function returns an two dimensional Array
#  $c[int: x][string: 'variable']: string
#  $c[int: x][string: 'value']: integer

#  Documentation of function get_url_string()
#  The function returns the URL variables in a string

#  Documentation of function get_dirs(int: count, int: result type)
#  Get the directories in the URL subclustered in the active domain
#  The argument influences the result by the following:
#  $count = 1; the last subdirectory, $count = 2; the secondlast subdirectory
#  $count = 0 or empty: All subdirectories
#  You can have a result as a string or array
#  result_type = 1 returns an array
#  result_type = 0 returns a string

#  Documentation of function get_active_dir()


class tx_mailform_urlHandler
{
 /**
 * Class entities
 */
 public $url;
 
 
 
 /**
 * @return Void
 * @desc Constructor
 */
 public function __construct()
 {
  $this->main();
 }
 
 public function main()
 {
  $this->url = "";
 }
 
 /**
 * @return Array
 * @desc Returns the url variables in a two dimensional array
 */
 public function get_url_array()
 {
  
  $a = $this->get_url_dirvar();
  
  
  $b = split("\&", $a[1]);
  
  for($x = 0; $x < sizeof($b); $x ++)
  {
   $d = split("\=", $b[$x]);

   $c[$x]['variable'] = $d[0];
   $c[$x]['value'] = $d[1];
   
   settype($c[$x]['value'], int);
  }
  
  return $c;
 }
 
 
 /**
 * @return Array
 * @desc Return the directories and url variables
 */
 private function get_url_dirvar()
 {
  return $a = split("\?", $_SERVER['REQUEST_URI'], 2);
 }
 
 
 
 
 /**
  * Returns a string of the current url vars
  *   
  * @param array $removeVar An one-dimensional array with get var names
  * @return String
  * @desc Returns all url variables
  */
 public function getCurrentUrl($removeVar = 0, $setQuestionMark = true, $parseAmp=true)
 {
  $a = $this->get_url_dirvar();
  $b = split("\&",$a[1]);
  
  if($removeVar == 0)
  {
    return $this->appendQuestionMark($a[1], $setQuestionMark);
  }
  else
  {
    for($x = 0; $x < sizeof($b); $x++)
    {
      $u = split ('=', $b[$x], 3);
      if($removeVar != 0)
      {
        $c = true;
        for($y = 0; $y < sizeof($removeVar); $y++)
          if($u[0] == $removeVar[$y])  $c = false;   
      }
      if($c)
      {
        if($d != "")
       	 $d .= $parseAmp ? "&amp;" : "&";
        
        $d .= $b[$x];
      }
    }
  }

  return $this->appendQuestionMark($d, $setQuestionMark);
 }
 
 /**
  * Appends a question mark to a specified string at the beginning
  *
  *    
  *@param Boolean $setQuestionMark
  *@param String $string
  */       
 private function appendQuestionMark($string, $setQuestionMark) {
  // Set Question mark (YES || NO) */
  assert(is_bool($setQuestionMark));
  $string = $setQuestionMark ? '?'.$string : $string;
  
  return $string;
 }
 

 
 /**
 * @return String/Array
 * @desc Returns the last subdirectory in your root
 * @param $count integer
 * @param $type integer
 */
 public function get_dirs($count = 0, $type = 0)
 {
  $a = $this->get_url_dirvar();
  if($count < 0)
  {
   for($x = (sizeof($b) - 1); $x >= $count; $x--)
   {
    $string .= "\/".$a[$x];
    $array[] = "\/".$a[$x];
   }
   if($type = 0)
    return $string;
   elseif($type = 1)
    return $array;
   else
    trigger_error("Wrong variable type selected (Array [1], String [0] are available)", E_USER_ERROR);
  }
  else
  return $a[0];
 }
 
 
 /**
 * @return String
 * @desc Returns the last subdirectory in your root
 */
 public function get_active_dir()
 {
  
  $a = $this->get_url_dirvar();
  $b = split("\/", $a);
  
  return "\/".$b[sizeof($b) - 1];
 }
 
 
 //End of class URL CONTROL
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/class.tx_mailform_urlHandler.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/class.tx_mailform_urlHandler.php']);
}

?>