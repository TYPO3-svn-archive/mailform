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
* mailform module class.tx_mailform_funcLib
*
* @author       Sebastian Winterhalder <sw@internetgalerie.ch>
*
*/
class tx_mailform_funcLib  {

	const ARRAY_PREFIX = 'uName';
	const FORM_POST_PREFIX = 'MFFORM';

	/**
	 * getUniqueFieldname($configData, $uName)
	 *
	 * @param Array $configData
	 * @param String $uName
	 * @return String
	 */
	public static function getUniqueFieldname($configData, $uName="") {
		if($uName == "") {
 			$uName = tx_mailform_funcLib::generateName($configData);
		}

		if(tx_mailform_funcLib::isUniqueFieldname($configData, $uName))
			return $uName;
		else
			return tx_mailform_funcLib::getUniqueFieldname($configData, tx_mailform_funcLib::generateName());
	}

	/**
	 * generateName($configData)
	 *
	 * @param Array $configData
	 * @return String
	 */
	private static function generateName($configData) {
		$res = "F";

		$res .= tx_mailform_funcLib::generateRandomString(2);
		$res .= rand(0,9);
		$res .= rand(0,9);
		$res .= rand(0,9);
		$res .= tx_mailform_funcLib::generateRandomString(3);
		return $res;
	}

	/**
	 * generateRandomString($length)
	 *
	 * @param int $length
	 * @return String
	 */
	private static function generateRandomString($length) {
		$chr = "";
		for($x = 0; $x < $length; $x ++) {
			$chr .= chr(rand(66,90));
		}
		return $chr;
	}

	/**
	 * Check if given field name is unique in array
	 *
	 */
	public static function isUniqueFieldname($configData, $uName) {
		return tx_mailform_funcLib::existsXTimes($configData, $uName);
	}

	/**
	 * Exists Time
	 *
	 * @param Array $configData
	 * @param String $uName
	 * @param int $times
	 * @return int
	 */
	public static function existsXTimes($configData, $uName, $times = 0) {
		$c = 0;
		if(!is_array($configData))
			throw new Exception('Config Data is Empty, it should be an array');

		foreach($configData as $page) {
			foreach($page as $field) {
				if($field['uName'] == $uName) {
					$c++;
				}
			}
		}
		return (intval($times) == $c);
	}

	/**
	 * Get an Unqiue Mailid
	 *
	 */
	public static function getUniqueMailid() {
		if(!t3lib_extMgm::isLoaded('mailform_statistics'))
			throw new Exception('Ext: mailform_statistics is not Loaded');
		$mres = $GLOBALS['TYPO3_DB']->sql_query("SELECT MAX(mailid) FROM tx_mailformstatistics_mails");
		$res = $GLOBALS['TYPO3_DB']->sql_fetch_row($mres);
		return $res[0] + 1;
	}

	/**
	 * shortenText($text, $len)
	 *
	 * @param String $text
	 * @param Int $len
	 * @return String
	 */
	public static function shortenText($text, $len) {
		if(strlen($text) > $len - 3) {
				$arr = array();
			for($x = 0; $x < $len - 3; $x++) {
				$arr[] = $text[$x];
			}
				return implode($arr)."...";
		}
		else
			return $text;
	}

	/**
	 * Remove Quotationmark
	 *
	 * @param String $string
	 * @param String $quote
	 * @return String
	 */
	public static function removeQuotationmark($string, $quote='"') {
		return str_replace($quote, '\'', $string);
	}

	/**
	 *  Get the field Object with unique Field ID
	 *
	 *@param $ufid String Field ID
	 *@param $objArray Array Object Fields
	 *@return Object
	 */
	public static function getFieldObject($ufid, $objArray) {
		foreach($objArray as $obj) {
			if($obj->getForm()->getUFID() == $ufid) {
				return $obj;
			}
		}
	}

	/**
	 * Create a new Field
	 *
	 * @param String $ufid
	 * @param Array $fieldArr
	 * @return Object
	 */
	public static function createField($ufid, $fieldArr) {
		require_once(t3lib_extMgm::extPath('mailform')."formTypesModel/class.tx_mailform_form.php");

		if(!is_array($fieldArr))
			throw new Exception('Is not an array $fieldArr: '.$fieldArr);

		foreach($fieldArr as $pageKey => $page) {
			foreach($page as $fieldKey => $field) {
				if($field['uName'] == $ufid) {
					$formInstance = t3lib_div::makeInstance('tx_mailform_form');
					$formInstance->setupForm($field, null, null);
					$formInstance->getForm()->setFEPageVars($pageKey, $fieldKey);
					return $formInstance;
				}
			}
		}
		throw new Exception("UFID '".$ufid."' does not exist in field configuration");
	}

	/**
	 * Convert an array to an CSV String
	 *
	 * @param Array $array
	 * @return String
	 */
  	public static function convertToCSV($array) {
  		if(is_array($array))
			return implode(',',$array);
		else
			return '';
	}

	/**
	 * Convert a CSV String to an array
	 *
	 * @param String $csv
	 * @return Array
	 */
	public static function convertFromCSV($csv) {
		if($csv == "")
			return array();
		return split(',',$csv);
	}

	/**
	 * Enter description here...
	 *
	 * @param Array $array
	 * @param String $assocKey
	 * @param Boolean $asc
	 * @return Array
	 */
	public static function insertionSort($array, $assocKey, $asc=true) {
	$i = $j = $index = 0;
		for($i = 1; $i < count($array); $i++) {
			$index = $array[$i];
			$j = $i;
			while($j > 0 && tx_mailform_funcLib::isStringGreaterThan($array[$j-1][$assocKey], $index[$assocKey], $asc)) {
				$array[$j] = $array[$j-1];
				$j = $j - 1;
			}
			$array[$j] = $index;
		}
		return $array;
	}

	/**
	 * Enter description here...
	 *
	 * @param String $string1
	 * @param String $string2
	 * @param Boolean $asc
	 * @return Boolean
	 */
	public static function isStringGreaterThan($string1, $string2, $asc=true) {
		for($x = 0; $x < strlen($string1); $x++) {
			if($asc) {
				if(ord($string[$x]) > ord($string2[$x]))
					return true;
				elseif(ord($string[$x] < ord($string2[$x])))
					return false;
				return true;
			} else {
				if(ord($string[$x]) < ord($string2[$x]))
					return true;
				elseif(ord($string[$x] > ord($string2[$x])))
					return false;
				return true;
			}
		}
		return true;
	}

	/**
	 * Sort an array with the index
	 *
	 * @param Array $array
	 * @return Array
	 */
	public static function sortArrayWithAscIndex($array) {
		$r1 = array();

		foreach($array as $key => $value) {
			if(!is_array($value))
				$r1[] = $value;
			else
				$r1[] = tx_mailform_funcLib::sortArrayWithAscIndex($value);
			}

		$res = $r1;
		return $res;
	}

	/**
	 * parseExtPath($path)
	 *
	 * @param String $path
	 * @param Boolean $checkPath=false
	 * @return String
	 */
	public static function parseExtPath($path, $checkPath=false, $mode=0) {
		$result = "";
		preg_match("(EXT:[a-zA-Z_]+/*)", $path, $treffer);
		if(count($treffer) > 0) {
			$extPath = str_replace("EXT:", "", $treffer[0]);
			$extPath = str_replace("/", "", $extPath);
			if(t3lib_extMgm::isLoaded($extPath)) {
				switch($mode) {
					case 0: $result .= t3lib_extMgm::extPath($extPath); break;
					case 1: $result .= t3lib_extMgm::extRelPath($extPath); break;
					case 2: $result .= t3lib_extMgm::siteRelPath($extPath); break;
				}
				
			} else {
			}
		}

		$result .= str_replace($treffer, "", $path);

		if(!@file_exists($result) && $checkPath == true) {
			throw new Exception("Path $path does not exist");
		}
		
		return $result;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $array
	 * @param unknown_type $current
	 * @param unknown_type $value
	 * @param unknown_type $afterCurrent
	 * @return unknown
	 */
	public static function insertIntoArray($array, $current, $value, $afterCurrent=true) {
		
		
		return $array;
	}
	
	
	/**
	 * Delete an Index from the wanted array, and move the values
	 * Works only with integer as index
	 *
	 * @param unknown_type $array
	 * @param unknown_type $index
	 * @return unknown
	 */
	public static function removeFromArray($array, $index) {
		require_once(t3lib_extMgm::extPath('mailform')."lib/datastructures/class.tx_mailform_ArrayList.php");

		if(!is_array($array))
			throw new Exception("Array Excepted");
		
		for($x = $index; $x < count($array)-1; $x++) {
			$field = $array[$x+1];
			foreach($field as $row) {
				foreach($row as $col) {
					$col->setPage($x);
				}
			}
			$array[$x] = $field;
		}
		
		unset($array[count($array)-1]);
		
		return $array;
	}
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/class.tx_mailform_funcLib.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/class.tx_mailform_funcLib.php']);
}

?>