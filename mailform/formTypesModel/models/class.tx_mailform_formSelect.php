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
require_once(t3lib_extMgm::extPath("mailform")."/formTypesModel/class.tx_mailform_formMultiple.php");
require_once(t3lib_extMgm::extPath("mailform")."/lib/layout/form/class.tx_mailform_option.php");
require_once(t3lib_extMgm::extPath("mailform")."/lib/layout/form/class.tx_mailform_select.php");

/**
 * tx_mailform_formSelect
 *
 * @author	Sebastian Winterhalder <typo3@internetgalerie.ch>
 *
 */
class tx_mailform_formSelect extends tx_mailform_formMultiple {

	protected $requireBox = true; // Display require box
	protected $form_select_size = 1;
	protected $be_typeImage = '../gfx/type/select.gif';

	/**
	 * Initialization
	 *
	 */
	protected function fieldInit() {
		$this->initMultiple();
		$this->hasInitialized = true;
	}

	/**
	 * Load child Data, The function can be overwritten by child classes
	 * Called in Render Frontend
	 *
	 */
	protected function loadChildData() {} // This Function can be overwritten by child classes
	
	/**
	 * renderFrontend();
	 *
	 * @return String
	 */
	protected function renderFrontend() {
		$this->initMultiple();
		$this->loadChildData();
		$this->configData['forms_select_size'] = (intval($this->configData['forms_select_size']) > 0) ? intval($this->configData['forms_select_size']) : 1;

		$select = new tx_mailform_select();
		$select->addCssClass('tx_mailform_select');
		$un1F = !empty($this->configData['forms_select_multiple']) ? '[]' : '';

		$select->setName($this->getUniqueFieldname().$un1F);
		$select->setSize($this->configData['forms_select_size']);
		$select->setId($this->getUniqueIDName('input'));
		if(!empty($this->configData['forms_select_multiple']))
			$select->setMultiple(true);
			
		// Separate standard value CSV;
		$standardValues = ($this->useRequestValue() && ($this->getRequestValue() != "")) ? split(",", $this->getRequestValue()) : split(",", $this->configData['form_standard_value']);

		foreach($this->configData['multiple_option'] as $key => $lineElement) {
			$option = new tx_mailform_option();
			if(is_array($lineElement)) {
				if(isset($lineElement['email']))
					$option->setValue($key);
				else
					$option->setValue($lineElement['value']);
				$option->setContent($lineElement['display']);
				$option->addCssClass('tx_mailform_option');
			} else {
				$option->setValue($lineElement);
				$option->setContent($lineElement);
			}
			$option->addCssClass('tx_mailform_option');

			if (	isset($this->postData[$this->configData['type']]) ) {
						if	(
									(is_array($this->postData[$this->configData['type']])
										&&	array_search($option->getValue()->getAttributeValue(), $this->postData[$this->configData['type']]) !== false )
										||	(!is_array($this->postData[$this->configData['type']]) && $this->postData[$this->configData['type']] == $option->getValue()->getAttributeValue())
								) {	 
									$option->setSelected(true);
								}
				}
			else {
				if(array_search($option->getValue()->getAttributeValue(), $standardValues) !== false)
					$option->setSelected(true);
				else
					$option->setSelected(false);
			}

			$select->addContent($option);
		}
		return $this->getWithTemplate($this->configData['label'], $select->getElementRendered(), $this->isFormRequired());
	}

	/**
	 * Render the HTML (Inherited from formAbstract)
	 *
	 *@return String
	 */
	protected function renderHtml() {
		$array = array_merge($this->getSelectOptions(),$this->getMultipleHtml());
		return $array;
	}

	/**
	* Get Select Options (HTML)
	*
	*@return String
	*/
	private function getSelectOptions() {
		global $LANG;
		$multiple = $this->makeCheckbox('forms_select_multiple', $this->forms_select_multiple)." ".$LANG->getLL('forms_select_allowMultiple');

		$array = array();
		$array = array_merge($array, $this->row_preRequiredSpecValue(true));
		$array[] = $this->makeRow($LANG->getLL('forms_select_size'), $this->makeInputField('forms_select_size', $this->forms_select_size).$multiple);
		$required = $this->makeCheckbox('forms_select_multiple', $this->forms_select_multiple)." ".$LANG->getLL('forms_select_allowMultiple');
		$array[]	= $this->makeRow($LANG->getLL('form_standard_value'), $this->makeInputField('form_standard_value', $this->configData['form_standard_value']));
		
		return $array;
	}

	/**
	 * enteredRequired()
	 * Inherit from tx_mailform_formAbstract
	 *
	 * @return Boolean
	 */
	public function enteredRequired() {
		if($this->isFormRequired()) {
			return (($this->postData[$this->configData['type']] != $this->configData['forms_required_nopass_value'])
					&& ($this->postData[$this->configData['type']] != $this->configData['form_standard_value']));
		}
		else {
			return true;
		}
	}

	/**
	 * getFieldValue()
	 *
	 * @return String
	 */
  	public function getFieldValue() {
		if(!empty($this->postData)) {
			return $this->postData['select'];
		}
		else
			return $this->configData['input_field_value'];
	}

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
	 * getEmailValue($rawText)
	 * Inherit from tx_mailform_formAbstract
	 *
	 * @param Boolean $rawText
	 * @return String
	 */
	public function getEmailValue($rawText = true) {

		if(is_array($this->postData[$this->configData['type']])) {
			$res = '';
			foreach($this->postData[$this->configData['type']] as $key) {
				if(array_key_exists($key, $this->configData['multiple_option'])) {
					if( ($this->configData['multiple_option'][$key]['email'] == "on" || $this->configData['multiple_option'][$key]['email'] == 1)) {
						$isEmail = true;
					} else {
						$isEmail = false;
					}
				}

				if($rawText) {
					$res .= $this->configData['multiple_option'][$key]['display']." (".$this->configData['multiple_option'][$key]['display'].")\n";
				} else {
					if($isEmail) {
						$res .= $this->configData['multiple_option'][$key]['display']." (".$this->configData['multiple_option'][$key]['display'].")<br />";
					} else {
						$res .= $key."<br />";
					}
				}
			}
			return $res;
		} else {
			if(array_key_exists($this->postData[$this->configData['type']], $this->configData['multiple_option'])) {
					if( ($this->configData['multiple_option'][$this->postData[$this->configData['type']]]['email'] == "on" || $this->configData['multiple_option'][$this->postData[$this->configData['type']]]['email'] == 1)) {
						$isEmail = true;
					} else {
						$isEmail = false;
					}
				}
			if($isEmail) {
				$res = $this->configData['multiple_option'][$this->postData[$this->configData['type']]]['display']." (".$this->configData['multiple_option'][$this->postData[$this->configData['type']]]['display'].")";
			} else {
				$res = $this->postData[$this->configData['type']]."<br>";
			}

			return $res;
		}
	}

	/**
	 * validFieldPost()
	 * Inherit from tx_mailform_formAbstract
	 * Select cannot have any invalid values
	 * 
	 * @return Boolean
	 */
	public function validFieldPost() { return true; }
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_formSelect.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_formSelect.php']);
}
?>