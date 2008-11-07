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
require_once (t3lib_extMgm::extPath("mailform")."/formTypesModel/class.tx_mailform_formMultiple.php");
  
/**
 * mailform module tt_content_tx_mailform_forms
 *
 * @author       Sebastian Winterhalder <typo3@internetgalerie.ch>
 * @since 30.07.2008
 * 
 */
class tx_mailform_formCheckbox extends tx_mailform_formMultiple {

protected $requireBox = true;
	// Frontend field requires post when post is sent
	// Overwrite from mailform_formAbstract
	protected $postRequired = false;
	protected $be_typeImage = '../gfx/type/checkbox.gif';
	
	/**
	 * Function Field Initialization
	 *
	 */
	protected function fieldInit() {
		global $FE_Handler;
		$this->hasInitialized = true;
		if(is_object($FE_Handler))
			$this->templateObj = $FE_Handler->getTemplateParser()->getTemplateObject('CHECKBOX','form');
	}

	/**
	 * Returns ' checked' if the given index of checkboxes is checked by post
	 *
	 * @param String $value
	 * @return String
	 */
	protected function isChecked($arg) {
		$checked_tmp =  'checked="checked" ';

		$key = array_search($arg, $this->configData['multiple_option']);
		$value = array_search($key, array_keys($this->configData['multiple_option']));

		// Separate standard value CSV;
		$standardValues = ($this->useRequestValue() && ($this->getRequestValue() != "")) ? split(",", $this->getRequestValue()) : split(",", $this->configData['form_standard_value']);

		$res = '';
		if(!empty($this->postData[$this->configData['type']])) {
			if(!empty($this->postData[$this->configData['type']][$value]))
				return $checked_tmp;
		} else {
			// Nicht selektieren, falls die Post nicht gesetzt ist
			for($x = 0; $x < count($standardValues); $x++) {
				if($this->configData['multiple_option'][$key]['value'] == $standardValues[$x] && $standardValues[$x] != "") {
					return $checked_tmp;
				}	
			}
		}
		return $res;
	}
	
	/**
	 * Render Frontend
	 *
	 * Will be inherited by Checkbox and Radio
	 * 
	 * @return String
	 */
	protected function renderFrontend() {
		global $FE_Handler;
		
		$this->initMultiple();
		if(!is_array($this->configData['multiple_option'])) {
				$this->configData['multiple_option'] = array();
		}

		$wrap_tmpl = $this->templateObject->getSubElement('WRAP', 'subel');
		$tmp = $this->templateObject->getSubElement('SINGLEELEMENT', 'subel');
		$count_elements = $element = 0;
		$result = "";
		foreach($this->configData['multiple_option'] as $boxKey => $lineOption) {
			
			if(!is_array($lineOption)) {
				$lineOption['value'] = $lineOption;
				$lineOption['display'] = $lineOption;
			}
			
			$subTemplate = clone $tmp;
			$subTemplate->addOutput("FIELDID", $this->getUniqueIDName('div').'-'.$element);
			$subTemplate->addOutput("FIELDVALUE", $this->getUniqueFieldname().'['.$element.']');
			$subTemplate->addOutput("FIELDNAME", $this->getUniqueFieldname().'['.$element.']');
			$subTemplate->addOutput('SELECTED', $this->isChecked($lineOption));
			$subTemplate->addOutput('FIELDDISPLAY', $lineOption['display']);
			$subTemplate->addOutput('CLASS', $this->getUniqueIDName('class').'-'.$element);
	
			$fields .= $subTemplate->getParsedHtml();

			if($this->configData['build_rowcount'] <= ($count_elements+1)) {
				$count_elements = 0;
				$wrapTemplate = clone $wrap_tmpl;
				$wrapTemplate->addOutput('ELEMENTS', $fields);
				$result .= $wrapTemplate->getParsedHtml();
				$fields = "";
			} else {
				$count_elements++;
			}
			$element++;
		}
		if($count_elements != 0) {
			$wrapTemplate = clone $wrap_tmpl;
			$wrapTemplate->addOutput('ELEMENTS', $fields);
			$result .= $wrapTemplate->getParsedHtml();
		}
		// Delete SubElement Parts from Template
		$this->templateObject->addOutput('SINGLEELEMENT', '');
		$this->templateObject->addOutput('WRAP', '');
		$checkHidden = '<input type="hidden" value="1" name="'.$this->getUniqueFieldName().'[checkbox_sent]" />';
		$this->templateObject->addOutput('FORMHIDDEN', $checkHidden);
		
		return $this->getWithTemplate($this->configData['label'], $result);
	}
	
	/**
	 *
	 * Backend Code
	 *
	 **/
	protected function renderHtml() {
		global $LANG;
		$array = array();

		if(isset($this->configData['required'])) {
			$array[] = $this->makeRow($LANG->getLL('required_values'), $this->makeInputField('required_values', $this->configData['required_values']), "(".$LANG->getLL('only_when_required').")");
		}

		$array[] = $this->makeTitleRow($LANG->getLL('form_options'));
		// Setup build horder field
		//$array[] = $this->makeRow($LANG->getLL('forms_buildOrder'), $this->makeCheckbox('build_vertical'));
		$rowCount = isset($this->configData['build_rowcount']) ? $this->configData['build_rowcount'] : $this->default_row_elements;
		$array[] = $this->makeRow($LANG->getLL('forms_countTillBreak'), $this->makeInputField('build_rowcount', $rowCount));
		$array[] = $this->makeRow($LANG->getLL('form_standard_value'), $this->makeInputField('form_standard_value', $this->configData['form_standard_value']));
		
		$array = array_merge($array, $this->getMultipleHtml());
		return $array;
	}

	public function getEmailValue($rawText) {
		global $LANG;
		if(empty($this->configData['multiple_option']))
			$this->configData['multiple_option'] = array();
		
		if($rawText) {
			
			
			
		$emailRes = '';
		 
		 $keys = array_keys($this->configData['multiple_option']);
			$array = $this->configData['multiple_option'];
			for($key = 0; $key < sizeof($keys); $key++) {
				$confkey = $this->getUniqueFieldname().'['.$key.']';
				$checked = false;
				
				if(empty($this->postData[$this->configData['type']]))
					$this->postData[$this->configData['type']] = array();
				$postKeys = array_keys($this->postData[$this->configData['type']]);
				for($x = 0; $x < sizeof($postKeys); $x++) {
					if($confkey == $this->postData[$this->configData['type']][$postKeys[$x]]) {
						$checked = true;
					}
				}
				if($emailRes != '')
					$emailRes .= ",";
				$emailRes .= $array[$keys[$key]]['display'];
			}
			$emailRes .= '';
			
			
			
			
			
			
			
			
			
			/*
			$label = "";
			for($x = 0; $x <= $this->labelLength; $x++)
				$label .= " ";

			$emailRes = "";

			$len = 0;
			$keys = array_keys($this->configData['multiple_option']);
			$array = $this->configData['multiple_option'];
			for($key = 0; $key < sizeof($keys); $key++) {
				if($len < strlen($array[$keys[$key]]))
					$len = strlen($array[$keys[$key]]);
				$labels[$key] = $array[$keys[$key]];

				$confkey = $this->getUniqueFieldname().'['.$key.']';
				$checked = false;
				if(empty($this->postData[$this->configData['type']]))
					$this->postData[$this->configData['type']] = array();
				$postKeys = array_keys($this->postData[$this->configData['type']]);
				for($x = 0; $x < sizeof($postKeys); $x++) {
					if($confkey == $this->postData[$this->configData['type']][$postKeys[$x]]) {
						$checked = true;
					}
				}

				if($checked) {
					$value[$key] .= "Selected";
				}
				else {
					$value[$key] .= "Not Selected";
				}
			}

			for($x = 0; $x < sizeof($labels); $x++) {
				$c = "";
				for($y = 0; $y < ($len-strlen($labels[$x])); $y++)
					$c .= " ";

				// Dont set spaces to the first line, because the outer function does this too.
				$preLabel = ($x == 0) ? "" : $label;

				$emailRes .= $preLabel."[$x] ".$labels[$x].": ".$c.$value[$x]."\n";
			}
		 // Endif ($rawText)
*/
		} else {
		 // Html output
		 $emailRes = '<table cellpadding="0" cellspacing="0" border="0" width="100%">
		 ';
		 
		 $keys = array_keys($this->configData['multiple_option']);
			$array = $this->configData['multiple_option'];
			for($key = 0; $key < sizeof($keys); $key++) {
				$confkey = $this->getUniqueFieldname().'['.$key.']';
				$checked = false;
				
				if(empty($this->postData[$this->configData['type']]))
					$this->postData[$this->configData['type']] = array();
				$postKeys = array_keys($this->postData[$this->configData['type']]);
				for($x = 0; $x < sizeof($postKeys); $x++) {
					if($confkey == $this->postData[$this->configData['type']][$postKeys[$x]]) {
						$checked = true;
					}
				}
				$emailRes .= '<tr><td style="border-bottom: 1px solid #EEE; text-align: left;">'.$array[$keys[$key]]['display'].'</td><td style="border-bottom: 1px solid #EEE; font-weight: bold;" width="10" align="right">'.($checked ? 'X':'-').'</td></tr>
				';
			}
			$emailRes .= '</table>
			';
			}
		return $emailRes;
	}
	
	/** Overwrite */
	public function getValueForFile() {
		$xmlArr = t3lib_div::xml2array($this->statValue['content_text']);
		
		$string = "";
		foreach($xmlArr as $xmlField) {
			$string .= $xmlField;
		}
		
		return $string;
	}
	
	/**
	 *
	 * Setter and getter
	 * Overwrite from tx_mailform_abstract	 
	 *
	 */				 	
	
	/**
	 *
	 * Get the current field content
	 *
	 */				 	
	public function getCurrentContent() {
		if(!empty($this->_FieldValue))
			return $this->_FieldValue->getCSV('content_text');
	}

	/**
	 * enteredRequired()
	 *
	 * @return unknown
	 */
	public function enteredRequired() {
		if(empty($this->configData['multiple_option']))
			$this->configData['multiple_option'] = array();
	
		if(!$this->isFormRequired()) {
			return true;
		}
		
		$sentValues = array();
		if(!is_array($this->postData[$this->configData['type']])) {
			$this->postData[$this->configData['type']] = array();
		}
		$sentKeys = array_keys($this->postData[$this->configData['type']]);
		foreach($sentKeys as $k) {
			if($k !== 'checkbox_sent') {
				$sentValues[] = $this->configData['multiple_option'][$this->multiplePrefix.$k];
			}
		}

		$requiredValues = split(",", $this->configData['required_values']);
		for($x = 0; $x < sizeof($requiredValues); $x++) {
			$notFoundflag = true;
			foreach($sentValues as $value) {
				if($requiredValues[$x] == $value['value']) {
					$notFoundflag = false;
				}
			}
			if($notFoundflag) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Validate the Field
	 *
	 */
	public function validateField() {
		if(!$this->alreadyValidated) {
			parent::validateField();
		}
	}
	
	/**
	 * getFieldValue()
	 *
	 * @return String
	 */
	public function getFieldValue() {
		if(!empty($this->postData))
			return $this->postData['checkbox'];
		else
			return $this->configData['input_field_value'];
	}
	
	/**
	 * validFieldPost()
	 *
	 * @return Boolean
	 */
	public function validFieldPost() {
		if($this->enteredRequired()) {
			return true;
		}
		else
			return false;
	}
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_formCheckbox.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_formCheckbox.php']);
}
?>