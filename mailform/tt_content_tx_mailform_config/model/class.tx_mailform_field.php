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
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/div/class.tx_mailform_div.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/form/class.tx_mailform_select.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/templateParser/class.tx_mailform_parseEngine.php");
/**
 * Class tx_mailform_field
 *
 */
class tx_mailform_field {
	private $page = 0;
	private $width = 0;
	private $cssClass = 'td-no-style';
	private $height = 0;
	private $colspan = 1;
	private $rowspan = 1;
	private $condition = '(1)';
	private $conditionActivated = false;
	
	private $elementOpenDisplay = false;
	
	private $formElement = array();
	
	private $fieldContentElements = array();
	
	private $rowIndex;
	private $colIndex;
	
	private $placeHolder = false;
	
	public static $fieldPrefix = 'field_config_';
	
	public static $fieldVars = array('edtCellCol', 'edtCellRow', 'addFtFieldRow', 'splitCellLcol', 'splitCellLrow', 'splitCellDcol', 'splitCellDrow', 'addFtFieldCol', 'newFieldIndex', 'fMdr', 'fMdc', 'fAdr', 'fAdc', 'sField', 'page', 'movFormTo', 'movFormIndex');

	public function __construct($row, $col, $page, $colspan=1, $rowspan=1) {
		$this->setIndex($row, $col);
		$this->setColspan($colspan);
		$this->setRowspan($rowspan);
		$this->page = $page;
	}

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $row
	 * @param unknown_type $col
	 */
	public function setIndex($row, $col) {
		$this->rowIndex = $row;
		$this->colIndex = $col;
	}

	/**
	 * Initialise containing field elements
	 * An array of field keys
	 *
	 * @deprecated Version 0.8.5
	 * @param Array $forms
	 */
	public function setForms($forms) {
		if(!is_array($forms))
			throw new Exception('Wrong argument passed. Array expected');
		$this->formElement = $forms;
	}


	/**
	 * Returns a Boolean value if the current field is a placeholder
	 *
	 * @return Boolean
	 */
	public function isPlaceholder() {
		return ($this->placeHolder != false);
	}
	
	/**
	 * get Place holder Index
	 * 
	 * Returns false if no index is set
	 *  -> if no index set: the field is a true field
	 * Returns an array (x,y) if the field is a placeholder
	 *  -> a Placeholder for another field that has rowspan / colspan > 1
	 * 
	 * 
	 * @return Mixed
	 */
	public function getPlaceholderIndex() {
		return $this->placeHolder;
	}
	
	/**
	 * get Containing Form Keys
	 *
	 * @return Array
	 */
	public function getContainingFormKeys() {
		return $this->formElement;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param Int $rowIndex
	 * @param Int $colindex
	 */
	public function setPlaceholderIndex($rowIndex, $colindex, $page) {
		$this->placeHolder = array($rowIndex, $colindex, $page);
	}
	
	/**
	 * Set this field to a true field
	 * Unset Placeholder status
	 *
	 */
	public function unsetPlaceholder() {
		$this->placeHolder = false;
	}
	
	public function getElementRendered() {
		global $LANG;
		$popup_info = '<b>'.$LANG->getLL('fWiz_configInfo').'</b><br>';
		$popup_info .= $LANG->getLL('fWiz_row').': '.$this->rowIndex.'<br>';
		$popup_info .= $LANG->getLL('fWiz_col').': '.$this->colIndex.'<br>';
		$popup_info .= $LANG->getLL('fWiz_rowspan').': '.$this->rowspan.'<br>';
		$popup_info .= $LANG->getLL('fWiz_colspan').': '.$this->colspan.'<br>';
	
		$fieldInfo = '<a style="pointer:none;" onmouseover="return overlib(\''.$popup_info.'\');" onmouseout="return nd();"><img style="cursor: help;" src="../gfx/help.gif" alt="" border="0"></a>';

	
		return '
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="wizard-field-table">
			<tr>
				<td valign="top" align="left" class="wizard-field-navigation" style="min-width:75px;"><!-- Navigation Element -->'.$this->getWizNaviElement().'<!-- END Navigation Element --></td>
				<td valign="top" align="right" class="wizard-field-navigation">'.$fieldInfo.'</td>
			</tr>
			'.$this->getEditFieldElement().'
			<tr><td class="wizard-field-body" colspan="2">'.$this->getWizContentBody('Field', $this->elementOpenDisplay).'</td></tr>
		</table>
		';
	}

	/**
	 * get edit field element
	 *
	 * @return String
	 */
	private function getEditFieldElement() {
	  global $LANG;
	  $result = '';
	  if(isset($_GET['edtCellRow']) && isset($_GET['edtCellRow']) && $_GET['edtCellRow'] == $this->rowIndex && $_GET['edtCellCol'] == $this->colIndex) {
			$checked = $this->getConditionActivated() ? " checked" : "";
			$vis = $this->getConditionActivated() ? "display:block;" : "display:none;";
			
	  		$result = '
			<tr><td colspan="2" class="wizard-field-navigation">
			<table cellpadding="1" cellspacing="0" border="0" width="100%">
				<tr>
					<td valign="top"><b>'.$LANG->getLL('fWiz_width').'</b></td>
					<td valign="top"><input type="hidden" value="'.$this->colIndex.'" name="mailform_fieldConf_col"><input type="hidden" value="'.$this->page.'" name="mailform_fieldConf_page"><input type="hidden" value="'.$this->rowIndex.'" name="mailform_fieldConf_row"><input type="text" name="mailform_fieldConf_width" value="'.$this->width.'" size="8"></td>
					<td valign="top"><b>'.$LANG->getLL('fWiz_css_class').'</b></td>
					<td valign="top"><input type="text" size="15" name="mailform_fieldConf_cssclass" value="'.$this->cssClass.'"></td>
					<td valign="top" width="*" align="right"><input type="submit" value="'.$LANG->getLL('fWiz_saveButton').'" onclick="this.form.submit()"></td>
				</tr>
				<tr>
					<td valign="top"><b>'.$LANG->getLL('fWiz_height').'</b></td>
					<td valign="top"><input type="text" name="mailform_fieldConf_height" value="'.$this->height.'" size="8"></td>
					<td colspan="3" valign="top"><input onchange="changeVisibility(\'mailform_fieldConf_condition_div\')" type="checkbox" id="mailform_fieldConf_activateCondition" name="mailform_fieldConf_activateCondition" '.$checked.'/><b><label for="mailform_fieldConf_activateCondition">'.$LANG->getLL('fWiz_page_condition_display').'</label></b>
						<div id="mailform_fieldConf_condition_div" style="'.$vis.'"><textarea style="display:visible" name="mailform_fieldConf_condition" rows="5" cols="50">'.$this->condition.'</textarea></div>
					</td>
				</tr>
			</table>
			</td></tr>
			';
		}
		return $result;
	}
	
	/**
	 * get wiz navi element
	 *
	 * @return unknown
	 */
	private function getWizNaviElement() {
	  global $LANG;
		$urlHandler = new tx_mailform_urlHandler();
		
		if($this->colspan > 1)
			$split_cell_right = '<a href="'.$urlHandler->getCurrentUrl(tx_mailform_wizard::getVars()).'&amp;splitCellLrow='.$this->rowIndex.'&amp;splitCellLcol='.$this->colIndex.'"><img src="../gfx/split_cell.gif" alt="'.$LANG->getLL('split_cell_right').'" title="'.$LANG->getLL('split_cell_right').'"></a>';
		else $split_cell_right = '';
		
		if($this->rowspan > 1)
			$split_cell_down = '<a href="'.$urlHandler->getCurrentUrl(tx_mailform_wizard::getVars()).'&amp;splitCellDrow='.$this->rowIndex.'&amp;splitCellDcol='.$this->colIndex.'"><img src="../gfx/split_cell_down.gif" alt="'.$LANG->getLL('split_cell_underneath').'" title="'.$LANG->getLL('split_cell_underneath').'"></a>';
		
		
		return '
		<a href="'.$urlHandler->getCurrentUrl(tx_mailform_wizard::getVars()).'&amp;mrgCellDrow='.$this->rowIndex.'&amp;mrgCellDcol='.$this->colIndex.'"><img src="../gfx/merge_cell_down.gif" alt="'.$LANG->getLL('merge_cell_underneath').'" title="'.$LANG->getLL('merge_cell_underneath').'"></a>
		'.$split_cell_down.'
		<a href="'.$urlHandler->getCurrentUrl(tx_mailform_wizard::getVars()).'&amp;mrgCellLrow='.$this->rowIndex.'&amp;mrgCellLcol='.$this->colIndex.'"><img src="../gfx/merge_cell.gif" alt="'.$LANG->getLL('merge_cell_left').'" title="'.$LANG->getLL('merge_cell_left').'"></a>
		'.$split_cell_right.'
		<a href="'.$urlHandler->getCurrentUrl(array_merge(tx_mailform_wizard::getVars(), array('edtCellRow', 'edtCellCol'))).'&amp;edtCellRow='.$this->rowIndex.'&amp;edtCellCol='.$this->colIndex.'"><img src="../gfx/edit2.gif" alt="'.$LANG->getLL('fWiz_editField').'" title="'.$LANG->getLL('fWiz_editField').'"></a>
		';
	}
	
	/**
	 * get wiz content body
	 *
	 * @param unknown_type $content
	 * @param unknown_type $open
	 * @return unknown
	 */
	private function getWizContentBody($content, $open=false) {
		global $LANG;
		
		$tmfH = tx_mailform_formHandler::getInstance();
		$urlHandler = new tx_mailform_urlHandler();
		$table = new tx_mailform_table();
		$table->setWidth('100%');
		$table->setCellpadding(0);
		$table->setCellspacing(0);

		$row = new tx_mailform_tr();
		$cell = new tx_mailform_td();
		$tmfH = tx_mailform_formHandler::getInstance();
		$cell->setContent('<a href="'.$urlHandler->getCurrentUrl(tx_mailform_wizard::getVars()).'&amp;addFtFieldRow='.$this->rowIndex.'&amp;addFtFieldCol='.$this->colIndex.'&amp;newField='.$tmfH->getUniqueFieldName().'&amp;newFieldIndex=-2#fieldWizard"><img style="margin-left:10px; margin-bottom: 4px; margin-top: 4px;" src="../gfx/new_el.gif" border="0" alt="" /></a>');
		$row->addTd($cell);
		$table->addRow($row);

		if(isset($_GET['newFieldIndex']) && $_GET['newFieldIndex'] == -2) {
			$this->addNewFormWizSelector($table, $_GET['newFieldIndex']);
		}

		foreach($this->formElement as $fieldIndex => $formKey) {

			try {
				$form = $tmfH->getForm($formKey);

				$row = new tx_mailform_tr();
				$cell = new tx_mailform_td();

				$cell->setContent($form->getForm()->getBE_HtmlPreview($this->rowIndex, $this->colIndex, $this->page, $fieldIndex, count($this->formElement)));
				$row->addTd($cell);
				$table->addRow($row);
				
				
			} catch (Exception $e) {
				unset($this->formElement[array_search($formKey, $this->formElement)]);
			}
			
			if(isset($_GET['newFieldIndex']) && $_GET['newFieldIndex'] == $fieldIndex) {
					$this->addNewFormWizSelector($table, $_GET['newFieldIndex']);
			}
		}
		
		return $table->getElementRendered();
	}
	
	private function addNewFormWizSelector(&$table, $innerIndex) {
		global $LANG;
	
		$tmfH = tx_mailform_formHandler::getInstance();
		$urlHandler = new tx_mailform_urlHandler();
		if(isset($_GET['addFtFieldRow']) && isset($_GET['addFtFieldCol']) && $_GET['addFtFieldRow'] == $this->rowIndex && $_GET['addFtFieldCol'] == $this->colIndex) {
			$innerTable = new tx_mailform_table();
			$innerTable->setCellpadding(0);
			$innerTable->setCellspacing(0);
			$innerTable->setBorder(false);
			$innerTable->addCssClass('wizard-form-preview-table');
			$row = new tx_mailform_tr();
			$col = new tx_mailform_td();


			$title = '<b>'.$LANG->getLL('fWiz_add_to_field').'</b>';

			$select = new tx_mailform_select();
			$select->setOnchange('window.location.href=\''.$urlHandler->getCurrentUrl(tx_mailform_wizard::getVars()).'&amp;page='.$this->page.'&amp;fAdc='.$this->colIndex.'&amp;fAdr='.$this->rowIndex.'&amp;sField=\'+(this.value)+\'&amp;sFieldIndex='.$innerIndex.'\'');
			$select->setName('mailform_addField');
			$opt = new tx_mailform_option();


			$opt->setValue($tmfH->getUniqueFieldName());
			$opt->setContent("--- ".$LANG->getLL('fWiz_choose_new_formElement')." ---");
			$select->addContent($opt);

			$configData = $tmfH->getConfigData();

			$tempForm = new tx_mailform_form();
			$tempForm->setupForm(array(), array(), null);
			
			// Prepare list of already used form types in the configuration field
			$static_cfData = tx_mailform_configData::getInstance();
			$tmpFieldData = $static_cfData->getFieldData();
			$alreadyUsedForms = array();
			foreach($tmpFieldData as $tmpField) {
				if($tmpField != '')
					$alreadyUsedForms = array_merge($alreadyUsedForms, split(',', $tmpField['field_config_form_keys']));
			}
			unset($tmpFieldData);
			unset($static_cfData);
			
			foreach($configData as $pKey => $page) {
				foreach($page as $fKey => $formConfig) {
				
				  $tmp_Form = new tx_mailform_form();
				  $tmp_Form->setupForm($formConfig, null, null);
				  
				  if( (array_search($formConfig['uName'], $this->formElement) === false) &&
				      (array_search($formConfig['uName'], $alreadyUsedForms) === false)	||
                !$tmp_Form->getForm()->isFormSingleUse()
							) {
						$opt = new tx_mailform_option();
						$opt->setValue($formConfig['uName']);
						
						if(!empty($formConfig['label']))
							$label = tx_mailform_funcLib::shortenText($formConfig['label'], 20);
						else
							$label = $LANG->getLL('forms_type').": "
							.$tempForm->getForm()->getLLOfFormType($formConfig['type']);
							
						$opt->setContent("[".$formConfig['uName']."] ".$label);
						$select->addContent($opt);
					}
				}
			}

			require_once(t3lib_extMgm::extPath('mailform').'lib/layout/form/class.tx_mailform_input.php');
			$hidden = new tx_mailform_input();
			$hidden->setName('mailform_addFieldRow');
			$hidden->setType('hidden');
			$hidden->setValue($this->rowIndex);
			$hid = $hidden->getElementRendered();
			$hidden->setName('mailform_addFieldCol');
			$hidden->setValue($this->colIndex);
			$hid2 = $hidden->getElementRendered();
			
      		$hidden->setName('mailform_addFieldInnerPosi');
			$hidden->setValue($innerIndex);
			$hid3 = $hidden->getElementRendered();
			$col->setContent($title."<br>".$select->getElementRendered().$hid.$hid2.$hid3);

			$row->addTd($col);
			$innerTable->addRow($row);

			$row = new tx_mailform_tr();
			$cell = new tx_mailform_td();
			$cell->setContent($innerTable->getElementRendered());
			$row->addTd($cell);
			$table->addRow($row);
		}
	}
	
	/**
	 * Parse field Body
	 *
	 * @param Array $formInstances
	 * @return Object
	 */
	public function p1_getFieldBody($formInstances) {
		// Outer TD Element of the field
		$td = new tx_mailform_td();
		$td->setColspan($this->getColspan());
		$td->setRowspan($this->getRowspan());
		if($this->getWidth() > 0)
			$td->setWidth($this->getWidth());
		if($this->getHeight() > 0)
			$td->setHeight($this->getHeight());
		$td->addCssClass($this->getCssClass());
		$td->setRowspan($this->getRowspan());

		// Make an inner Div Element
		$innerDiv = new tx_mailform_div();
		$innerDiv->setId("div-outer-id-".$this->page."-".$this->colIndex."-".$this->rowIndex);
		
		// Parse the Condition if set
		if($this->getBooleanOfCondition()) {
			$innerDiv->addStyle("display:block;");
		} else {
			$innerDiv->addStyle("display:none;");
		}
		
		$formElements = $this->getFormElements();
		foreach($formElements as $form) {
			$result .= $form->getForm()->getFEHtml();
		}
		
		$innerDiv->setContent($result);
		$td->setContent($innerDiv->getElementRendered());
		return $td;
	}

	public function getColspan() {
		if($this->colspan <= 0)
			$this->setColspan(1);
		return $this->colspan;
	}
	
	public function setColspan($colspan) {
		$this->colspan = $colspan;
	}
	
	public function getRowspan() {
		if($this->rowspan <= 0)
			$this->setRowspan(1);
		return $this->rowspan;
	}
	
	public function setRowspan($rowspan) {
		$this->rowspan = $rowspan;
	}
	
	public function setCssClass($cssClass) {
		$this->cssClass = $cssClass;
	}
	
	public function getCssClass() {
		return $this->cssClass;
	}
	
	public function getWidth() {
		return $this->width;
	}
	
	public function setWidth($width) {
		$this->width = $width;
	}
	
	public function getHeight() {
		return $this->height;
	}
	
	public function setPage($page) {
		$this->page = $page;
	}
	
	public function getPage() {
		return $this->page;
	}
	
	public function setHeight($height) {
		$this->height = $height;
	}
	
	public function getRowIndex() {
		return $this->rowIndex;
	}
	
	public function getColIndex() {
		return $this->colIndex;
	}
	
	/**
	 * Sets the Condition string
	 *
	 * @param String $string
	 */
	public function setCondition($string) {
		$this->condition = $string;
	}
	
	/**
	 * Get the condition string (needs to be parsed with parseEngine)
	 *
	 * @return String
	 */
	public function getCondition() {
		return $this->condition;
	}
	
	/**
	 * Set if the field uses condition
	 *
	 * @param Boolean $boolean
	 */
	public function setConditionActivated($boolean) {
		$this->conditionActivated = $boolean;
	}
	
	/**
	 * Get boolean if the field has conditions whether to display or not
	 *
	 * @return Boolean
	 */
	public function getConditionActivated() {
		if($this->conditionActivated || strlen($this->conditionActivated) > 0)
			return true;
		else
			return false;
	}
	
	/**
	 * get Boolean of given condition;
	 * If Condition is not activated; return true
	 * 
	 * @return Boolean
	 */
	public function getBooleanOfCondition() {
		// Parse the Condition if set
		if($this->getConditionActivated()) {
			$parseEngine = new tx_mailform_parseEngine();
			$parseEngine->loadData($this->getCondition());
			
			if($parseEngine->getParsed() != 0 && $parseEngine->getParsed() != false) {
				return true;
			} else {
				return false;
			}
		} else return true;
	}

	public function loadFromConfig($fieldConfig) {
		$this->setColspan($fieldConfig[tx_mailform_field::$fieldPrefix.'colspan']);
		$this->setRowspan($fieldConfig[tx_mailform_field::$fieldPrefix.'rowspan']);
		$this->setCssClass($fieldConfig[tx_mailform_field::$fieldPrefix.'cssclass']);
		$this->colIndex = $fieldConfig[tx_mailform_field::$fieldPrefix.'colIndex'];
		$this->rowIndex = $fieldConfig[tx_mailform_field::$fieldPrefix.'rowIndex'];
		$this->setWidth($fieldConfig[tx_mailform_field::$fieldPrefix.'width']);
		$this->setHeight($fieldConfig[tx_mailform_field::$fieldPrefix.'height']);
		$this->setCondition($fieldConfig[tx_mailform_field::$fieldPrefix.'condition']);
		$this->formElement = tx_mailform_funcLib::convertFromCSV($fieldConfig[tx_mailform_field::$fieldPrefix.'form_keys']);
		$this->setPage($fieldConfig[tx_mailform_field::$fieldPrefix.'page']);
		$this->setConditionActivated($fieldConfig[tx_mailform_field::$fieldPrefix.'activateCondition']);
		$arr = tx_mailform_funcLib::convertFromCSV($fieldConfig[tx_mailform_field::$fieldPrefix.'placeholder_index']);
		if(count($arr) == 0)
		$this->placeHolder = false;
		else
			$this->setPlaceholderIndex($arr[0],$arr[1],$arr[2]);
	}
	
	public function addFormElement($formkey, $innerIndex) {
		if(array_search($formkey, $this->formElement) === false) {
		  if($innerIndex < 0){
	      for($x = (count($this->formElement)-1); $x >= 0; $x--) {
	      	$this->formElement[$x+1] = $this->formElement[$x];
				}
				$this->formElement[0] = $formkey;
			} else {
	      for($x = (count($this->formElement)-1); $x > $innerIndex; $x--) {
	      	$this->formElement[$x+1] = $this->formElement[$x];
				}
				$this->formElement[$innerIndex+1] = $formkey;
			}

			$this->formElement = tx_mailform_funcLib::sortArrayWithAscIndex($this->formElement);
		} else {

		}
	}
	
	public function removeFormElement($arrayIndex) {
		unset($this->formElement[$arrayIndex]);
		$this->formElement = tx_mailform_funcLib::sortArrayWithAscIndex($this->formElement);
	}
	
	public function removeAllFormElementWithUID($formkey) {
		foreach($this->formElement as $key => $keys) {
			if($formkey == $keys) 
				unset($this->formElement[$key]);
		}
		tx_mailform_funcLib::sortArrayWithAscIndex($this->formElement);
	}
	
	/**
	 * switch positions of a field $fromCurrId to $fromTargetId
	 *
	 * @param Integer $formCurrId
	 * @param Integer $formTargetId
	 */
	public function moveFormInField($formCurrId, $formTargetId) {
		$curr = $this->formElement[$formCurrId];
		$tmp = $this->formElement[$formTargetId];

		$this->formElement[$formTargetId] = $curr;
		$this->formElement[$formCurrId] = $tmp;
	}
	
	/**
	 * get all form elements of this field
	 *
	 * @return Array
	 */
	public function getFormElements() {
		return $this->formElement;
	}
	
	/**
	 * get current config data of this field
	 *
	 * @return Array
	 */
	public function getConfigData() {
		$pref = 'field_config_';
		$plcIndex = ($this->placeHolder) ? tx_mailform_funcLib::convertToCSV($this->placeHolder) : "";

		$array = array (
			$pref.'colspan' => $this->getColspan(),
			$pref.'rowspan' => $this->getRowspan(),
			$pref.'colIndex' => $this->getColIndex(),
			$pref.'rowIndex' => $this->getRowIndex(),
			$pref.'cssclass' => $this->getCssClass(),
			$pref.'width' => $this->getWidth(),
			$pref.'height' => $this->getHeight(),
			$pref.'condition' => $this->getCondition(),
			$pref.'activateCondition' => $this->getConditionActivated(),
			$pref.'page' => $this->getPage(),
			$pref.'placeholder_index' => $plcIndex,
			$pref.'form_keys' => tx_mailform_funcLib::convertToCSV($this->formElement)
		);
		
		return $array;
	}
	
	public function __toString() {
		return "Mailform Field: [".$this->getColIndex()."] [".$this->getRowIndex()."] [".$this->getPage()."]";
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/tt_content_tx_mailform_config/model/class.tx_mailform_field.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/tt_content_tx_mailform_config/model/class.tx_mailform_field.php']);
}
?>