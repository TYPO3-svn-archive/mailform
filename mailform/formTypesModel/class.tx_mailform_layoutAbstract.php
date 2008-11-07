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
require_once(t3lib_extMgm::extPath("mailform")."/formTypesModel/class.tx_mailform_formAbstract.php");

/**
* mailform module tt_content_tx_mailform_forms
*
* @to add an additional form type, register the form type in tx_mailform_form.
*
* @author       Sebastian Winterhalder <sw@internetgalerie.ch>
* 
*/
abstract class tx_mailform_layoutAbstract extends tx_mailform_formAbstract {
  
    /** Overwrite from tx_mailform_formAbstract */
    public function setupCurrentContent($dbFieldRow) {}
    
    /** Overwrite from tx_mailform_formAbstract */
    public function getCurrentContent() { return null; }
		protected $singleUse = false; // Overwrite $singleUse, field can multiply being used
		
    /** Overwrite from tx_mailform_formAbstract */
    protected function row_PreShowIfContainsValue() { return null; }
    
   /**
   *
   *@return String;
   */     
	public function getEmailResult($rawText=true) {
		$res = "";
		if( !(isset($this->configData['disable_field_on_email']) && $this->getEmailValue=="") && (empty($this->configData['disable_field_on_email']) || $this->configData['disable_field_on_email'] != "on")) {
			if($rawText) {
				$len = $this->labelLength - strlen($this->configData['label']);
			
				$res .= $this->getLabel().":";
				if($len > 100)
					$res .= "\n";
					for($x = 0; $x < $len; $x ++)
						$res .= " ";
					$res .= $this->getEmailValue($rawText);
				if($len > 100)
					$res = $res."\n";
				return $res."\n";
			}
			else {
				$iRow = new tx_mailform_tr();
				
				$iTd = new tx_mailform_td();
				$iTd->setValign('top');
				$iTd->setColspan(2);
				//$iTd->setAlign('right');
				$iTd->addCssClass('mailContent');
				$iTd->setContent($this->getEmailValue($rawText));
				$iRow->addTd($iTd);
				return $iRow;
			}
		}
		else return '';
	}
	
	// Inherit from tx_mailform_formAbstract
	public function enteredRequired() {
		return true;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/class.tx_mailform_layoutAbstract.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/class.tx_mailform_layoutAbstract.php']);
}