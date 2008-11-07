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
require_once(t3lib_extMgm::extPath("mailform")."formTypesModel/class.tx_mailform_xajaxHandler.php");
require_once(t3lib_extMgm::extPath("mailform")."lib/class.tx_mailform_fieldValueContainer.php");
require_once(t3lib_extMgm::extPath("mailform")."lib/templateParser/class.tx_mailform_templateParser.php");

/**
* mailform module tt_content_tx_mailform_forms
*
* @to add an additional form type, register the form type in tx_mailform_form.
*
* @author       Sebastian Winterhalder <sw@internetgalerie.ch>
*/
abstract class tx_mailform_formAbstract_State {

	/**
	 * getStateStar()
	 *
	 * If the Field is not Required
	 * 
	 * @param Boolean $required
	 * @param Integer $valid
	 * @return String
	 */
	protected function getStateStar($valid=-1) {
		if (!$this->isFormRequired())
			return '';
			
		if($this->isFormValid() && $this->enteredRequired()) {
			return '<span class="tx_mailform_required_star tx_mailform_required_star-ok">*</span>';
		} elseif($this->isFormValid() == 0) {
			return '<span class="tx_mailform_required_star tx_mailform_required_star-error" title="'.$this->configData['validation_error_message'].'">*</span>';
		} else {
			return '<span class="tx_mailform_required_star tx_mailform_required_star-required" title="'.$this->configData['validation_required_message'].'">*</span>';
		}
	}

	/**
	 * Get the image for validation
	 *
	 *@return String;
	 */
	protected function getStateImg($display_images=true) {
		global $plugin_configuration;
		$nr = $this->getStateNumber();
		
		switch($nr) {
			case 1:
				if($plugin_configuration['icon_display_ok'] && $display_images)
					return '<img src="'.tx_mailform_funcLib::parseExtPath($plugin_configuration['icon_ok'], false, 2).'" border="0" alt="" />';
				break;
			case 2:
				if($plugin_configuration['icon_display_required'] && $display_images)
					return '<img src="'.tx_mailform_funcLib::parseExtPath($plugin_configuration['icon_required'], false, 2).'" border="0" alt="" />';
				break;
			case 3:
				if($plugin_configuration['icon_display_error'] && $display_images)
					return '<img src="'.tx_mailform_funcLib::parseExtPath($plugin_configuration['icon_error'], false, 2).'" border="0" alt="'.$this->getFormErrorMsg().'" title="'.$this->getFormErrorMsg().'" />';
				break;
			case 4;
				return "";
			break;
			default:
				throw new Exception("Programming Error");
		}
	}

	/**
	 * Get the image for validation
	 *arguments
	 * $required should be true if user has entered required
	 * $required should be false if user has not entered required
	 * 
	 * $valid should be true if validation has passed
	 * 
	 *@return String;
	 */
	protected function getStateMessage(/*$unformatted=false*/) {
		$nr = $this->getStateNumber();
		$this->validateField();
		//print "StateNr: ".$this->getFormType()." - ".$nr."<br>";
		switch($nr) {
			case 1:
				return '';
			break;
			case 2:
				//if($unformatted)
				return $this->configData['validation_required_message'];
				//return '<span class="required required-msg-required">'.$this->configData['validation_required_message'].'</span>';
			break;
			case 3:
				return $this->getFormErrorMsg();
				//return '<div class="stateMessage">'.$this->getFormErrorMsg().'</div>';
			break;
			case 4:
				if($unformatted)
					return '';
				return '';
			break;
			default:
				throw new Exception("Programming Error");
		}
	}
	
	/**
	 * Get the CSS Class for the outer div in dependency of validation and requirements
	 *
	 * @return String
	 */
	public function getCSSClass() {
		$nr = $this->getStateNumber();

		switch($nr) {
			case 1:
				return "tx_mailform_valid";
			break;
			case 2:
				return "tx_mailform_required";
			break;
			case 3:
				return "tx_mailform_invalid";
			break;
			case 4:
				return "tx_mailform_none";
			break;
			default:
				throw new Exception("Programming Error");
		}
	}
	
	/**
	 * State Numbers
	 * 1: Required, entered, Validation OK
	 * 2: Required, not Entered, Validation OK,
	 * 3: Required, entered, Validation invalid
	 * 4: Not yet entered
	 * 
	 * 4: Required, not entered, Validation invalid
	 * 5: Not Required, Validation Ok => 5 = 1
	 * 6: Not Required, Validation invalid => 6 = 3
	 *
	 * @return unknown
	 */
	private function getStateNumber() {
		if(!$this->postVarSet()) {
			return 4;
		} else {
			if($this->hasFormValidation()) {
					if($this->isFormValid()) {
						if($this->isFormRequired()) {
							if($this->enteredRequired()) {
								return 1;
							} else {
								return 2;
							}
						} else {
							return 1;
						}
					} else {
						if($this->enteredRequired()) {
							return 3;
						} else {
							return 2;
					}
				}
			} else {
				if($this->isFormRequired()) {
					if($this->enteredRequired())
						return 1;
					else return 2;
				} else {
					return 1;
				}
			}
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/class.tx_mailform_formAbstract_State.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/class.tx_mailform_formAbstract_State.php']);
}
?>