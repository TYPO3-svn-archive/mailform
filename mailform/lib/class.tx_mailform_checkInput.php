<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
* Attribute Factory Class. Every Entity does manage his attributes with that factory
*
*
* PHP versions 4 and 5
*
* Copyright notice
*
* (c) 2007 Sebastian Winterhalder <sebi@concastic.ch>
* All rights reserved
*
* This script is part of the TYPO3 project. The TYPO3 project is
* free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
*
*/

## class to check if user input is valid

class tx_mailform_checkInput
{
	public function is_not_empty($tmp)
	{
		if($tmp != "")
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
	
	public function is_longer_than_x($tmp, $x)
	{
		if(strlen($tmp) > $x)
			return True;
		else
			return False;	
	}
	
	public function is_valid_password($tmp)
	{
		# checks if a string is valid for a password
		# that means: at least 5 characters which are prntable (ASCII 20 - 7E)
		
		if(preg_match("#^[\x20-\x7E]{5,}$#", $tmp))
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
	public function contains_word_character($tmp)
	{
		# checks if a string contains at least 1 word character

		if(preg_match("#([\w]{1})#", $tmp))
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
	
	public function contains_3_word_characters($tmp)
	{
		# checks if a string contains at least 3 word characters
		
		if(preg_match("#([\w]{1})(.*?)([\w]{1})(.*?)([\w]{1})#s", $tmp))
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
	
	public function contains_digit($tmp)
	{
		# checks if a string contains at least 1 digit
		
		if(preg_match("#([\d]{1})#", $tmp))
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
	
	public function contains_word_character_or_digit($tmp)
	{
		# checks if a string contains at least 1 word character or digit
		
		if(preg_match("#([\d\w]{1})#", $tmp))
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
	
	public function is_valid_email($tmp)
	{
		if(preg_match("#^([\w\d\.\_\-]+)@([\w\d\.\_\-]+)\.([\w]{1,})$#", $tmp) && strlen($tmp) <= 80)
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
	
	public function is_valid_url($tmp)
	{
		if(preg_match("#^(http|news|https|ftp|aim)://#i", $tmp) && strlen($tmp) <= 255)
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/class.tx_mailform_checkInput.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/class.tx_mailform_checkInput.php']);
}
?>