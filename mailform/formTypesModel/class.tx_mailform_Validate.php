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
 * tx_mailform_Validate
 *
 * @to add an additional form type, register the form type in tx_mailform_form.
 * @author       Sebastian Winterhalder <sw@internetgalerie.ch>
 * 
 */
class tx_mailform_Validate {

	public $errorMessage;
	/**
	 *
	 * Normal validation types
	 *
	 *@return Boolean
	 */
	public function isValidWithType($validationString, $method, $optionalRegex="") {
		$valid = false;
		switch($method){
		case "mail":
			$valid = t3lib_div::validEmail($validationString);
			$message = "Please enter a valid email adresse";
		break;
		case "alphanum":
		$exp = '/^[a-zA-Z0-9]+$/';
			$valid = preg_match($exp,$validationString);
			$message = "Please enter only numbers and letters";
		break;
		case "numbers":
			$valid = is_numeric($validationString);
			$message = "Please enter only numbers";
		break;
		case "letters":
		$exp = '/^[a-zA-Z]+$/';
			$valid = preg_match($exp,$validationString);
			$message = "Please enter only letters";
		break;
		case "regex_input":
			$valid = preg_match($optionalRegex,$validationString);
			$message = "The String does not match the requirements";
		break;
		case "value_check":
			$valid = ($validationString == $optionalRegex);
			$message = "The String does not match the requirements";
		break;
			default: $message = "This validation type does not exist: ".$method;
		}
		
		$this->errormessage = $message;
		
		return $valid;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/class.tx_mailform_Validate.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/class.tx_mailform_Validate.php']);
}