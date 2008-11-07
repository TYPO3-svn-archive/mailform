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



class tx_mailform_fieldValueContainer  {
  
  private $xml;
  
	/**
	 * Set XML
	 *
	 */        
	public function setXML($xmlString) {
		if(!empty($xmlString) && is_array(t3lib_div::xml2array($xmlString))) {
			$this->xml = $xmlString;
		}
		else{
			if(empty($xmlString))
				throw new Exception ("class.xmlObject function setXML( arg ) parameter arg is empty");
			if(is_array(t3lib_div::xml2array($xmlString)))
				throw new Exception ("class.xmlObject function setXML( arg ) invalid XML String");
		}
	}
  
	/**
	 * Set Array
	 *
	 * @param Array $array
	 */
	public function setArray($array) {
					
		if(!empty($array) && is_array($array)){
			$this->xml = t3lib_div::array2xml($array);
		} else
			throw new Exception ("class.xmlObject function setArray( arg ) parameter arg is empty or invalid");
	}
  
  /**
   * Get the Array from XML
   *
   *@return Array   
   */        
  public function getArray() {
    if(!empty($this->xml))
      return t3lib_div::xml2array($this->xml);
    else
      throw new Exception ("class.xmlObject contains no valid XML");
  }
  
  /**
   * Get CSV from XML
   *
   *@return Array   
   */        
  public function getCSV($datatyp = 'content_varchar') {
     $mainArray = t3lib_div::xml2array($this->xml);
	if(!is_array($mainArray)) $mainArray = array();
        
      $csv = array(); // Initialize $csv
      // Walk through all array elements and append them to $csv

      
      foreach($mainArray as $dim) {
      	if(!is_array($dim)) {
      		$arr = t3lib_div::xml2array($this->xml);
			if(!is_array($arr)) $arr = array();
			$csv = array_merge($csv, $arr);
      	} else {

      		if($datatyp == 'content_varchar') {
      			$arr = $dim[$datatyp];
      			$csv[] = $arr;
      		}
      		else {
      			$array = t3lib_div::xml2array($dim[$datatyp]);
      			
		      	if (is_array($array)){
					foreach ($array as $line) {
						if(is_array($line)) {
							if(!empty($line['value']) &&
							!empty($line['display'])) {
								$csv[] =
								$line['value'];//$line['display'].": ".$line['value'];
							} else {
								foreach ($line as $key => $line2) {
								if(is_array($line2)) {
								$csv[] = implode("," , $line2);
								} else {
								$csv[] = array($line2);
								}
								}
							}
						} else {
							$csv = array_merge($csv, array($line));
						}
					}
				} else {
					$csv[] = $array;
				}
      		}
      	}

      }
      
      $csvString = implode(',', $csv);

      return $csvString;
  }
  
   
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/class.tx_mailform_fieldValueContainer.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/class.tx_mailform_fieldValueContainer.php']);
}

?>