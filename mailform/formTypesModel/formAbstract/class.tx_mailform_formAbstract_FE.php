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
* mailform module tt_content_tx_mailform_forms
*
* @to add an additional form type, register the form type in tx_mailform_form.
*
* @author       Sebastian Winterhalder <sw@internetgalerie.ch>
*/
require_once(t3lib_extMgm::extPath("mailform")."formTypesModel/formAbstract/class.tx_mailform_formAbstract_BE.php");

abstract class tx_mailform_formAbstract_FE extends tx_mailform_formAbstract_BE {
	

	/**
	 * Get Field Post Value
	 *
	 * @deprecated
	 * @return Mixed
	 */
	public function getFieldPostValue() {
		$post = t3lib_div::_GP('tx_mailform');
		return $post[$this->pageNr][$this->fieldNr][$this->configData['type']];
	}
	
 	/**
 	 * Get an unique field name
 	 *
 	 * @param int $pageNr
 	 * @param int $fieldNr
 	 * @return String
 	 */
	public function getUniqueFieldname($pageNr=false, $fieldNr=false) {
		global $FE_Handler;
		return $this->formPrefix.'['.$FE_Handler->getMailformUID().']['.$this->getUFID().']['.$this->configData['type'].']';
	}
	
	/**
	 * Get an Unique ID Name for id tags (html)
	 *
	 * @param String $tagPrefix
	 * @param int $pageNr
	 * @param int $fieldNr
	 * @return String
	 */
	public function getUniqueIDName($tagPrefix = "div") {
		global $FE_Handler;
		if(strlen($tagPrefix) <= 0)
			$tagPrefix = "div";
		return "tx_mailform_".$tagPrefix.'-item-'.$FE_Handler->getMailformUID()."-".$this->getUFID();
	}
	
	/**
	 * Reset Form in Frontend, This is the Abstract function
	 * and has no effect if its not overwritten
	 *
	 * @return Boolean
	 */
	public function resetForm() {
		return true;
	}
	
	/**
	*  Return the rendered Frontend Form
	*
	* @return String
	*/
	public function getFEHtml() {
		global $FE_Handler;
		if(empty($this->configData['form_special_template']))
			$this->configData['form_special_template'] = tx_mailform_templateParser::$unallowedArrayKey;
		$this->templateObject = $FE_Handler->getTemplateParser()->getTemplateObject("",strtoupper($this->getFormType()), $this->configData['form_special_template']);

		if (($this->pageNr >= 0) && ($this->fieldNr >= 0) && $this->hasInitialized()) {
			if(isset($this->configData['display_field_in_form']))
				return '';
			else
				return $this->renderFrontend();
		}
		else
			return "Error: This object has not properly initialized for the Frontend. PageNr and FieldNr are needed. PageNr: $this->pageNr, FieldNr: $this->fieldNr";
	}

	/**
	 * get Email Result
	 *
	 * @param Boolean $rawText
	 * @return String
	 */
	public function getEmailResult($rawText=true) {
		$res = "";
		if( !(isset($this->configData['disable_field_on_email']) && $this->getEmailValue=="") && (empty($this->configData['disable_field_on_email']) || $this->configData['disable_field_on_email'] != "on")) {
			if($rawText) {
				
				if(!$this->isSingleElementDisplayed())
					return '';
				
				$len = $this->labelLength - strlen($this->configData['label']);

				$res .= $this->configData['label'].":";
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
				$fT = new tx_mailform_form();

				$iTd = new tx_mailform_td();
				$iTd->setValign('top');
				$iTd->addCssClass('mailLabel');
				if($this->isSingleElementDisplayed())
					$iTd->setContent(htmlspecialchars(ucfirst($this->configData['label'])));
				$iRow->addTd($iTd);

				$iTd = new tx_mailform_td();
				$iTd->setValign('top');
				$iTd->addCssClass('mailContent');
				if($this->isSingleElementDisplayed())
					$iTd->setContent($this->getEmailValue($rawText));
				$iRow->addTd($iTd);
				return $iRow;
			}
		}
		else return '';
	}

	/**
	 * get Email Value
	 *
	 * @param Boolean $rawText
	 * @return String
	 */
	public function getEmailValue($rawText=false) {
		return $this->postData[$this->configData['type']];
	}

	/**
	 * Returns the value which is sent by the user by post
	 *
	 * @return Mixed
	 */
	public function getPostValue() {
		$data = array();
		if(!is_array($this->postData[$this->getFormType()])) {
			$data = array('value' => $this->postData[$this->getFormType()],
						 'checked' => true,
						 'display' => $this->postData[$this->getFormType()],
						 'multiple' => false);
		}
		return $data;
	}
	
	/**
	 * Implemented in each formtype
	 * @access Frontend
	 *
	 */
	protected abstract function renderFrontend();

	/**
	 * Abstract Implementation from tx_mailform_formAbstract
	 *  Render the Field Template
	 */
	protected function renderTemplate($form, $label, $icon, $css, $outerFieldID, $inputFieldID, $array=array()) {
		if(!is_object($this->templateObject))
		  throw new Exception('Template Object not set');
		  
		$this->templateObject->addOutput('FORMELEMENT', $form);
		
		if(empty($this->configData['display_label'])) {
			$this->templateObject->addOutput('LABEL', $label);
		} else {
			$this->templateObject->addOutput('LABEL', '');
		}
		
		$errorMessage = $this->getStateMessage();
		$icon = $this->getStateImg();
		
		$this->validateField();
		
		if(($this->configData['display_error'] == 'on' && $this->displayError))
			{ $errorMessage=''; $array[0] = ''; $icon = ''; };
			
		$this->templateObject->addOutput('ERR_MSG', $this->getStateMessage());
		$this->templateObject->addOutput('ICON', $icon);
		$this->templateObject->addOutput('FIELD_ID', $outerFieldID);
		$this->templateObject->addOutput('EXTRA_CSS', $css);
		$this->templateObject->addOutput('EXTRA_STYLE', $array['extra_style']);
		$this->templateObject->addOutput('INPUT_ID', $inputFieldID);
		$this->templateObject->addOutput('DESCRIPTION', $this->configData['input_textarea_desc']);
		$this->templateObject->addOutput('REQUIRED_STAR', $this->getStateStar($this->isFormValid()));
		
		return $this->templateObject->getParsedHtml();
	}

	private $parseEngineAlreadyLoaded = false;
	private $displaySingleElement = true;
	protected function loadSingleParseEngine() {
		if($this->configData['display_field_condition_active'] && !$this->parseEngineAlreadyLoaded) {
			require_once(t3lib_extMgm::extPath('mailform')."lib/templateParser/class.tx_mailform_parseEngine.php");
			$parseEngine = new tx_mailform_parseEngine();
			$parseEngine->loadData($this->configData['display_field_condition']);
			if($parseEngine->getParsed() == false || $parseEngine->getParsed() == 0) {
				$this->displaySingleElement = false;
			} else {
				$this->displaySingleElement = true;
			}
			$this->parseEngineAlreadyLoaded = true;
		}
	}
	
	/**
	 * Gives Boolean if this formular is displayed by given conditions
	 *
	 * @return unknown
	 */
	public function isSingleElementDisplayed() {
		$this->loadSingleParseEngine();
		return $this->displaySingleElement;
	}
	
	/**
	 * Function that renders a standard template for all files
	 *
	 * @param String $label
	 * @param String $form
	 * @param Boolan $req
	 * @return String
	 */
	protected function getWithTemplate($label, $form, $req=false) {
		$this->validateField();

		while((strpos($label, "  ") !== false)) {
			str_replace("  ", " ", $label);
		}

		
		if($this->isSingleElementDisplayed()) {
			$style = 'display:block;';
		} else {
			$style = 'display:none;';
		}

		$outerID = $this->getUniqueIDName("div");
		$inputID = $this->getUniqueIDName("input");

		$additionalArguments = array(0 => $this->getStateMessage(),
										'extra_style' => $style,
										);
		
		return $this->renderTemplate($form,
										$label,
										$this->getStateImg(),
										$this->getCSSClass(),
										$outerID,
										$inputID,
										$additionalArguments
									);
	}
}

?>