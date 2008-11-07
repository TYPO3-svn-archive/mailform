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
* @author       Sebastian Winterhalder <sw@internetgalerie.ch>
*
*/
class tx_mailform_formCaptcha extends tx_mailform_formAbstract {

	protected $requireBox = false;
	protected $be_typeImage = '../gfx/type/captcha.gif';
	protected $used_captcha_ext;
	private $standard_captcha_ext = 'sr_freecap';

	/**
	 * Initialization
	 *
	 */
	protected function fieldInit() {
		$this->hasInitialized = true;
		$this->configData['validation_type'] = 'captcha';
	}

	/**
	 * determineExtension()
	 *
	 */
	private function determineExtension() {
		global $plugin_configuration;
		// code inserted to use free Captcha
		// Find out which extension to be used
		if($this->configData['forms_captcha_extension']) {
			switch($this->configData['forms_captcha_extension']) {
				case 'template': 
					if($plugin_configuration['captcha_extension_key'] == 'sr_freecap' || $plugin_configuration['captcha_extension_key'] == 'captcha')
						$this->used_captcha_ext = $plugin_configuration['captcha_extension_key'];
					else
						$this->used_captcha_ext = $this->standard_captcha_ext;
				break;
				case 'sr_freecap': $this->used_captcha_ext = 'sr_freecap'; break;
				case 'captcha': $this->used_captcha_ext = 'captcha'; break;
				default: $this->used_captcha_ext = $this->standard_captcha_ext; break;
			}
		} else {
			$this->used_captcha_ext = $this->standard_captcha_ext;
		}
	}
	
	/**
	 * RenderFrontend();
	 *
	 * @return String
	 */
	protected function renderFrontend() {
		$this->determineExtension();
		if(!t3lib_extMgm::isLoaded($this->used_captcha_ext)) {
			return $this->getWithTemplate($this->configData['label'], 'Extension: \''.$this->used_captcha_ext.'\' required, but not loaded', $this->isFormRequired(), -1);
		}
		
		$hiddenField = '<input type="hidden" value="1" name="'.tx_mailform_naviAbstract::getVarPrefix().'[submit]" />';
		$this->templateObject->addOutput("FORMHIDDEN", $hiddenField);
		
		$sizeHtml = (!empty($this->configData['input_form_cols'])) ? ' size="'.$this->configData['input_form_cols'].'"' : "";
		
/**
		 * Code if extension is loaded and selected
		 */		 		
		if($this->used_captcha_ext == 'sr_freecap') {
			require_once(t3lib_extMgm::extPath('sr_freecap').'pi1/class.tx_srfreecap_pi1.php');
			require_once(t3lib_extMgm::extPath('sr_freecap').'pi2/class.tx_srfreecap_pi2.php');
			
			$this->freeCap = t3lib_div::makeInstance('tx_srfreecap_pi2');
			
			if (is_object($this->freeCap)) {
				$img = $this->freeCap->makeCaptcha();
				$this->templateObject->addOutput('CAPTCHA_RELOAD', $img['###SR_FREECAP_CANT_READ###']);
				$this->templateObject->addOutput('CAPTCHA_NOTICE', $img['###SR_FREECAP_NOTICE###']);
				$this->templateObject->addOutput('CAPTCHA_IMAGE', $img['###SR_FREECAP_IMAGE###']);
		

				$field = '
					<input id="'.$this->getUniqueIDName("input").'" class="tx_mailform_captcha"'.$xajax.' name="'.$this->getUniqueFieldname().'" type="text"'.$sizeHtml.' />';

				return $this->getWithTemplate($this->configData['label'], $field, $this->isFormRequired(), -1);
			} else
				return 'Error With Captcha';

		} elseif($this->used_captcha_ext == 'captcha') {
			$this->templateObject->addOutput('CAPTCHA_RELOAD', '');
			$this->templateObject->addOutput('CAPTCHA_NOTICE', '');
			
			$captchaHTMLoutput = '<img src="'.t3lib_extMgm::siteRelPath('captcha').'captcha/captcha.php" alt="Captcha Image" title="Captcha Image"/>';
			$this->templateObject->addOutput('CAPTCHA_IMAGE', $captchaHTMLoutput);
			
		
			$field = '
					<input id="'.$this->getUniqueIDName("input").'" class="tx_mailform_captcha"'.$xajax.' name="'.$this->getUniqueFieldname().'" type="text"'.$sizeHtml.' />';

			return $this->getWithTemplate($this->configData['label'], $field, $this->isFormRequired(), -1);
		} else {
			die('<b>Unknown Error</b>: Configuration not properly set, or a captcha extension is not loaded!');
		}
	}

	/**
	 * renderHtml()
	 *
	 * @return Array
	 */
	protected function renderHtml() {
		global $LANG;
		$hidden = $this->makeHidden('validation_type', 'captcha');
		
		$array = array();
		$array[] = $this->makeTitleRow($LANG->getLL('form_options'));
		
		if(t3lib_extMgm::isLoaded('captcha') && t3lib_extMgm::isLoaded('sr_freecap')) {
			$content = array('template' => $LANG->getLL('forms_captcha_extension_template'), 'sr_freecap' => $LANG->getLL('forms_captcha_extension_srfreecap'), 'captcha' => $LANG->getLL('forms_captcha_extension_captcha'));
			$sel = $this->makeSelectbox('forms_captcha_extension', $content, $this->configData['forms_captcha_extension']);
			
			$array[] = $this->makeRow($LANG->getLL('forms_captcha_extension'), $sel);
		} elseif( t3lib_extMgm::isLoaded('captcha') || t3lib_extMgm::isLoaded('sr_freecap') ) {
			$array[] = $this->makeTwoColRow($LANG->getLL('forms_captcha_notFound'), '');
		} else {
			$array[] = $this->makeTwoColRow($LANG->getLL('forms_captcha_notFound'), '');
		}
		
		if(!isset($this->configData['form_input_cols']))
			$this->configData['form_input_cols'] = $this->input_field_cols;
		$array[] = $this->makeRow($LANG->getLL('form_input_cols'), $this->makeInputField('input_form_cols', $this->configData['form_input_cols']).$hidden);
		
		if(empty($this->configData['form_captcha_errmsg']))
			$this->configData['form_captcha_errmsg'] = $LANG->getLL('form_captcha_errmsg');
		$array[] = $this->makeRow($LANG->getLL('validation_error_message'), $this->makeInputField('form_captcha_errmsg', $this->configData['form_captcha_errmsg']));

		return $array;
	}

	/**
	 * setupCurrentContent($dbFieldRow)
	 *
	 * @param unknown_type $dbFieldRow
	 */
	public function setupCurrentContent($dbFieldRow) {
		$this->_FieldValue = t3lib_div::makeInstance("tx_mailform_fieldValueContainer");
		$this->_FieldValue->setArray(array($dbFieldRow['content_text']));
	}

	/**
	 * enteredRequired().
	 * Inherit from tx_mailform_formAbstract
	 *
	 * @return Boolean
	 */
	public function enteredRequired() {
		if($this->isFormRequired())
			return (strlen($this->postData[$this->configData['type']]) > 0);
		else
			return true;
	}

	/**
	 * validateField().
	 * Inherit from tx_mailform_formAbstract
	 *
	 * @return Array
	 */
	public function validateField() {
		// Make sure the field is not twice validated
		if($this->alreadyValidated)
			return false;
		else $this->alreadyValidated = true;

		$this->determineExtension();
		if($this->used_captcha_ext == 'sr_freecap') {
			require_once(t3lib_extMgm::extPath('mailform').'formTypesModel/class.tx_mailform_singletonCaptcha.php');
			$cap = tx_mailform_singletonCaptcha::getInstance($this->postData['captcha']);
			if($cap->isValid()) {
				$this->appendFormDataValid(1, "");
			}
			else {
				$this->appendFormDataValid(0, $this->configData['form_captcha_errmsg']);
			}
		} elseif($this->used_captcha_ext == 'captcha') {
			$captchaStr = $_SESSION['tx_captcha_string'];
			$_SESSION['tx_captcha_string'] = '';
			if($captchaStr && $this->postData['captcha']===$captchaStr)
				$this->appendFormDataValid(1, '');
			else
				$this->appendFormDataValid(0, $this->configData['form_captcha_errmsg']);
		} else {
			die('Unknown Error in tx_mailform_formCaptcha.php' );
		}
	}
	
	private function captchaValidated($bool) {
		$_SESSION['tx_mailform'][tx_mailform_FE_Handler::getContentUID()]['fe_content'][$this->getUFID()]['validate'] = true;
	}
	
	private function hasAlreadyValidated() {
		if(!isset($_SESSION['tx_mailform'][tx_mailform_FE_Handler::getContentUID()]['fe_content'][$this->getUFID()]['validate']))
			return false;
		return $_SESSION['tx_mailform'][tx_mailform_FE_Handler::getContentUID()]['fe_content'][$this->getUFID()]['validate'];
	}

	/**
	 *
	 */
	public function getFieldValue() {
		if(!empty($this->postData))
			return $this->postData['imgverification'];
		else
			return $this->configData['input_field_value'];
	}

	/**
	 * validFieldPost().
	 * Inherit from tx_mailform_formAbstract
	 *
	 * @return Boolean
	 */
	public function validFieldPost() {
		$this->determineExtension();
		if($this->used_captcha_ext == 'sr_freecap') {
			require_once(t3lib_extMgm::extPath('mailform').'formTypesModel/class.tx_mailform_singletonCaptcha.php');
			$cap = tx_mailform_singletonCaptcha::getInstance($this->postData['captcha']);
			return $cap->isValid();
		} elseif($this->used_captcha_ext == 'captcha') {
			$captchaStr = $_SESSION['tx_captcha_string'];
			$_SESSION['tx_captcha_string'] = '';
			return ($captchaStr && $this->postData['captcha']===$captchaStr);
		} else {
			die('Unknown Error in tx_mailform_formCaptcha.php' );
		}
	}

	public function imgverification_vreq($formArg, $pageNr, $fieldNr, $label, $validation_type, $add_regex) {
		global $LANG;

		//tx_mailform_xajaxHandler::getInstance()->getXajaxObject()->getFormValues('formId');
	
		// Instantiate the xajaxResponse object
		$objResponse = new tx_xajax_response();
		$string = $this->getUniqueFieldName("div", $pageNr, $fieldNr);
		foreach($formArg as $key => $value) {
			$string .= "[$key]=>$value:";
			foreach($value as $subKey => $val2) {
				$string .= "[$subKey]=>$val2:";
			}
		}
		$fieldValue = $formArg[$this->formPrefix][$pageNr][$fieldNr][$this->configData['type']];
		if($this->isFormRequired() && !$this->enteredRequired($fieldValue)) {
			$objResponse->addAssign($this->getUniqueIDName("td-inner", $pageNr, $fieldNr),"innerHTML",$this->getStateImg(true, -1));
			$objResponse->addAssign($this->getUniqueIDName("div", $pageNr, $fieldNr),"className", "tx_mailform_required");
		} elseif($this->isFormRequired()) {
			$objResponse->addAssign($this->getUniqueIDName("td-inner", $pageNr, $fieldNr),"innerHTML",$this->getStateImg(true, true));
			$objResponse->addAssign($this->getUniqueIDName("div", $pageNr, $fieldNr),"className", "tx_mailform_valid");		
		} else {
			$objResponse->addAssign($this->getUniqueIDName("td-inner", $pageNr, $fieldNr),"innerHTML",$this->getStateImg(false, -1));
			$objResponse->addAssign($this->getUniqueIDName("div", $pageNr, $fieldNr),"className", "tx_mailform_none");
		}
		//return the xajaxResponse object
		return $objResponse;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_formCaptcha.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_formCaptcha.php']);
}
?>