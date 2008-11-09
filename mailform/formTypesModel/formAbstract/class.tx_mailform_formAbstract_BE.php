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
require_once(t3lib_extMgm::extPath("mailform")."formTypesModel/class.tx_mailform_xajaxHandler.php");
require_once(t3lib_extMgm::extPath("mailform")."lib/class.tx_mailform_fieldValueContainer.php");
require_once(t3lib_extMgm::extPath("mailform")."lib/templateParser/class.tx_mailform_templateParser.php");
require_once(t3lib_extMgm::extPath("mailform")."formTypesModel/formAbstract/class.tx_mailform_formAbstract_State.php");

abstract class tx_mailform_formAbstract_BE extends tx_mailform_formAbstract_State {
  

	/**
	 * is Form Single Use
	 *
	 * @return Int
	 */
	public function isFormSingleUse() {
		return $this->singleUse;
	}

	/**
	 * get BE_Preview
	 *
	 * @return String
	 */
	public function getBE_Preview($table) {
		global $LANG;
		require_once(t3lib_extMgm::extPath('mailform')."lib/layout/table/class.tx_mailform_table.php");
		require_once(t3lib_extMgm::extPath('mailform')."lib/layout/form/class.tx_mailform_input.php");

		$row = new tx_mailform_tr();

		$col = new tx_mailform_td();
		if(!$this->isFormReferenceUsed()) {
			$col->setContent('<img style="cursor:help;" src="../gfx/no_reference.gif" onmouseover="return overlib(\''.$LANG->getLL('fWiz_reference_not_set').'\');" onmouseout="return nd();">');
		} else
			$col->setContent('<img style="cursor:help;" src="../gfx/reference_ok.gif" onmouseover="return overlib(\''.$LANG->getLL('fWiz_reference_ok').'\');" onmouseout="return nd();">');

		$col->setWidth('15');
		$row->addTd($col);

		$col = new tx_mailform_td();
		$col->setContent('['.$this->configData['uName'].']');
		$col->setWidth('140');
		$row->addTd($col);

		$col = new tx_mailform_td();
		$lbl = $this->configData['label'] != "" ? $this->configData['label'] : '['.$this->getLLOfFormType().']';
		$col->setContent($lbl);
		$col->setWidth(200);
		$row->addTd($col);

		$col = new tx_mailform_td();
			$innerTable = new tx_mailform_table();
			$innerTable->setCellpadding(1);
			$innerTable->setCellspacing(0);
			$innerTable->setWidth("100%");
			$innerTable->setBorder(false);
			$iRow = new tx_mailform_tr();
			$iCol = new tx_mailform_td();
			$iCol->setContent($this->getStatusImageFEMAIL());
			$iCol->setWidth("10px");
			$iRow->addTd($iCol);
			$iCol = new tx_mailform_td();
			$iCol->setContent($this->getStatusImageInfo());
			$iCol->setWidth("10px");
			$iRow->addTd($iCol);
			$iCol = new tx_mailform_td();
			$iCol->setContent('['.$this->getLLOfFormType().']');
			$iRow->addTd($iCol);
			$innerTable->addRow($iRow);
			
		$col->setContent($innerTable->getElementRendered());
		$col->setWidth(200);
		$row->addTd($col);
		
		$col = new tx_mailform_td();
		$col->setAlign('right');
		$urlHandler = new tx_mailform_urlHandler();
		$col->setContent('	<a href="'.$urlHandler->getCurrentUrl(array_merge(tx_mailform_wizard::getVars(), array('edtField', 'listFields'))).'&amp;edtField='.$this->configData['uName'].'"><img src="../gfx/edit2.gif" alt="'.$LANG->getLL('fWiz_edit_field').'" title="'.$LANG->getLL('fWiz_edit_field').'"></a>
												<a href="'.$urlHandler->getCurrentUrl(array_merge(tx_mailform_wizard::getVars(), array('delField', 'listFields'))).'&amp;delItemFromList='.$this->configData['uName'].'"><img src="../gfx/garbage.gif" alt="'.$LANG->getLL('form_delete_element').'" title="'.$LANG->getLL('form_delete_element').'"></a>');
		$row->addTd($col);

		$table->addRow($row);
		return $table;
	}

	/**
	 * getLLOfFormType
	 *
	 * @param Boolean $type
	 * @return String
	 */
	public function getLLOfFormType($type = false) {
		global $LANG;
		if($type != false)
			$this->configData['type'] = $type;

		$form = new tx_mailform_form();
		if($form->isValidFormType($this->configData['type']))
			return $LANG->getLL('forms_type_'.$this->configData['type']);
		elseif($form->isValidLayoutType($this->configData['type']))
			return $LANG->getLL('layout_type_'.$this->configData['type']);
		elseif($form->isValidNaviType($this->configData['type']))
			return $LANG->getLL('navi_type_'.$this->configData['type']);
		else
			return $LANG->getLL('type_not_found');
	}
	

	/**
	 * return the rendered Backend HTML
	 *
	 *@return String
	 */
	public function getHtml() {
		if($this->hasInitialized()) {
			return $this->renderStandardInputs();
		}
		else
			return "Current object has not jet been initialized!";
	}

	/**
	 * get BE_Html Preview
	 *
	 * @param Int $rowIndex
	 * @param Int $colIndex
	 * @param Int $page
	 * @param Int $fieldIndex
	 * @param Int $fieldCount
	 * @return String
	 */
	public function getBE_HtmlPreview($rowIndex, $colIndex, $page, $fieldIndex, $fieldCount) {
		global $LANG;
		$urlHan = new tx_mailform_urlHandler();
		$table = new tx_mailform_table();
		$table->setCellpadding(0);
		$table->setCellspacing(0);
		$table->setBorder(false);

		if(isset($_GET['edtField']) && $_GET['edtField'] == $this->configData['uName'])
			$table->addCssClass('wizard-form-preview-table-active');
		else
			$table->addCssClass('wizard-form-preview-table');
		$row = new tx_mailform_tr();
		$col = new tx_mailform_td();

		$col->setContent($this->getStatusImageFEMAIL());
		$col->setWidth('18');
		$row->addTd($col);

		$col = new tx_mailform_td();
		$col->setContent($this->getStatusImageInfo());
		$col->setWidth('18');
		$row->addTd($col);

		$col = new tx_mailform_td();
		$label = empty($this->configData['label']) ? "<b>".$LANG->getLL('forms_type').":</b> ".$this->getLLOfFormType()."" : "<b>".$LANG->getLL('forms_label').":</b> ".tx_mailform_funcLib::shortenText($this->configData['label'], 20);
		$col->setContent('<a href="'.$urlHan->getCurrentUrl(tx_mailform_wizard::getVars()).'&amp;page='.$page.'&amp;edtField='.$this->configData['uName'].'#fieldWizard">'.$label."</a>");
		$row->addTd($col);

		$col = new tx_mailform_td();
		$col->setAlign('right');

		if($fieldIndex != 0)
			$upLink = '<a href="'.$urlHan->getCurrentUrl(tx_mailform_wizard::getVars()).'&amp;page='.$page.'&amp;fMdr='.$rowIndex.'&amp;fMdc='.$colIndex.'&amp;movFormIndex='.$fieldIndex.'&amp;movFormTo='.(intval($fieldIndex)-1).'"><img src="../gfx/button_up.gif" border="0" alt="Feld nach oben verschieben"></a>';
		else
			$downLink = '<img src="../gfx/button_empty.gif" border="0" alt="">';
		if($fieldCount-1 > $fieldIndex)
			$downLink = '<a href="'.$urlHan->getCurrentUrl(tx_mailform_wizard::getVars()).'&amp;page='.$page.'&amp;fMdr='.$rowIndex.'&amp;fMdc='.$colIndex.'&amp;movFormIndex='.$fieldIndex.'&amp;movFormTo='.(intval($fieldIndex)+1).'"><img src="../gfx/button_down.gif" border="0" alt="Feld nach unten verschieben"></a>';
		else
			$downLink = '<img src="../gfx/button_empty.gif" border="0" alt="">';

		$tmfH = tx_mailform_formHandler::getInstance();
		$addLink = '<a href="'.$urlHan->getCurrentUrl(tx_mailform_wizard::getVars()).'&amp;addFtFieldRow='.$rowIndex.'&amp;addFtFieldCol='.$colIndex.'&amp;newField='.$tmfH->getUniqueFieldName().'&amp;newFieldIndex='.$fieldIndex.'#fieldWizard"><img src="../gfx/new_el.gif" border="0" title="'.$LANG->getLL('fWiz_addField_beneath').'" alt="'.$LANG->getLL('fWiz_addField_beneath').'"></a>';

		$col->setContent($upLink.$downLink.'<a href="'.$urlHan->getCurrentUrl(tx_mailform_wizard::getVars()).'&amp;edtField='.$this->configData['uName'].'#fieldWizard"><img src="../gfx/edit2.gif" border="0" title="'.$LANG->getLL('fWiz_edit_field').'" alt="'.$LANG->getLL('fWiz_edit_field').'"></a>
											<a href="'.$urlHan->getCurrentUrl(tx_mailform_wizard::getVars()).'&amp;delFfromF='.$fieldIndex.'&amp;fAdr='.$rowIndex.'&amp;fAdc='.$colIndex.'"><img src="../gfx/garbage.gif" border="0" title="'.$LANG->getLL('fWiz_remove_reference').'" alt="'.$LANG->getLL('fWiz_remove_reference').'"></a>'
											.$addLink	);

		$row->addTd($col);
		$table->addRow($row);

		$row = new tx_mailform_tr();
		$col = new tx_mailform_td();

		$col->setContent($content);
		$col->setColspan(3);
		$row->addTd($col);
		$table->addRow($row);

		return $table->getElementRendered();
	}

		
	/**
	 * Render all HTML
	 *
	 *@return String
	 */
	protected function renderStandardInputs() {

		global $LANG;
		require_once(t3lib_extMgm::extPath('mailform')."lib/layout/table/class.tx_mailform_table.php");
		require_once(t3lib_extMgm::extPath('mailform')."lib/layout/form/class.tx_mailform_input.php");
		$table = new tx_mailform_table();
		$table->setWidth("100%");
		$table->addCssClass('subPartTypeForm');
		$table->setCellpadding(2);
		$table->setCellspacing(0);
		$table->setBorder(false);
		$table->setComment('renderStandardInputs()');

		$row = new tx_mailform_tr();
		$col = new tx_mailform_td();
		$col->setColspan(2);
		$col->addCssClass('subPartTypeFormTitle');
		$hiddenField = new tx_mailform_input();
		$hiddenField->setType('hidden');
		$hiddenField->setName(tx_mailform_funcLib::FORM_POST_PREFIX.'[uName]');
		$hiddenField->setValue($this->configData['uName']);

		$col->setContent(ucfirst($this->getLLOfFormType()).": [".$this->configData['uName']."]".$this->configData['label'].$hiddenField->getElementRendered());
		$row->addTd($col);
		$table->addRow($row);

		// Formular Type
		$row = new tx_mailform_tr();
		$col = new tx_mailform_td();
		$col->addCssClass('subPartTypeForm');
		$col->setContent($LANG->getLL('forms_type'));
		$col->setValign(tx_mailform_attr_valign::VALIGN_MIDDLE );
		$row->addTd($col);
		$col = new tx_mailform_td();
		$col->addCssClass('subPartTypeForm');

		//TODO FIELD NAME
		$col->setContent($this->getSelectboxOfType());
		$col->setValign(tx_mailform_attr_valign::VALIGN_MIDDLE );
		$row->addTd($col);
		$table->addRow($row);

		// Formular Template
		if(empty($this->templateObject)) {
			$this->templateObject = tx_mailform_templateParser::getInstance();
		}
		
		$spTemp = $this->templateObject->getSpecialTemplates("",strtoupper($this->getFormType()));
		$spTemp = array_keys($spTemp);
		
		$ktemp[$stemp] = $LANG->getLL('fWiz_standard_template');
		foreach($spTemp as $stemp) {
			if($stemp != tx_mailform_templateParser::$unallowedArrayKey)
				$ktemp[$stemp] = $LANG->getLL('forms_type_'.$this->getFormType()).": ".$stemp;	
		}
		
		$templateOption = $this->makeSelectbox('form_special_template', $ktemp, $this->configData['form_special_template']);
		
		$row = new tx_mailform_tr();
		$col = new tx_mailform_td();
		$col->addCssClass('subPartTypeForm');
		$col->setContent($LANG->getLL('fWiz_choose_template'));
		$col->setValign(tx_mailform_attr_valign::VALIGN_MIDDLE );
		$row->addTd($col);
		$col = new tx_mailform_td();
		$col->addCssClass('subPartTypeForm');
		$col->setContent($templateOption);
		$col->setValign(tx_mailform_attr_valign::VALIGN_MIDDLE );
		$row->addTd($col);
		$table->addRow($row);
		
		$output = "\n<!-- renderStandardInputs() -->\n";
		if($this->labelField) {
			$table->addRow($this->makeRow($LANG->getLL('forms_type_label'), $this->makeInputField('label', '')));
			$table->addRow($this->makeRow($LANG->getLL('forms_display_label'), $this->makeCheckbox('display_label', ($xtmp = ($this->configData['display_label'] == "on" || $this->configData['display_label']) ? true : false))));
		}

		if($this->requireBox) {
			$t2 = new tx_mailform_table();
			$tr2 = new tx_mailform_tr();
			$td2 = new tx_mailform_td();
			$td2->setContent($this->makeCheckbox('required'));
			$tr2->addTd($td2);
			$td3 = new tx_mailform_td();
			$td3->setContent($this->makeInputField('validation_required_message', $LANG->getLL('validation_req_standard_msg')));
			$tr2->addTd($td3);
			$td4 = new tx_mailform_td();
			$td4->setContent('('.$LANG->getLL('validation_required_message').')');
			$tr2->addTd($td4);
			$t2->addRow($tr2);
			$table->addRow($this->makeRow($LANG->getLL('forms_required'), $t2->getElementRendered()));
		}
		
		if($this->displayError) {
			$table->addRow($this->makeRow($LANG->getLL('form_display_error'), $this->makeCheckbox('display_error', $this->configData['display_error'])));
		}

		$rows = $this->renderHtml();

		foreach($rows as $row) {
			$table->addRow($row);
		}

		$table->addRow($this->makeTitleRow($LANG->getLL('display options')));
		$preShowVal = $this->row_PreShowIfContainsValue();
		if($preShowVal != null)
			$table->addRow($this->row_PreShowIfContainsValue());

		$table->addRow($this->row_preDisplayFieldInForm());
		$table->addRow($this->row_preDisplayFieldCondition());
		$table->addRow($this->displayOptions());

		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mailform']['addOwnFieldOptionsHook'])) {
			
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mailform']['addOwnFieldOptionsHook'] as $_classRef) {
				//foreach($extension as $_classRef) {
					$_procObj = & t3lib_div::getUserObj($_classRef);
					$hookArray = $_procObj->addOwnFieldOptionsHook($this);
	
					//Titel der eigenen Sektion
					$table->addRow($this->makeTitleRow($hookArray['title']));

					$rows = $hookArray['rows'];
					foreach($rows as $row){
						
						$add_table = new tx_mailform_table();
						$add_tr = new tx_mailform_tr();
						$add_td = new tx_mailform_td();
	
						if ($row['type'] == 'input'){
							$add_td->setContent($this->makeInputField($hookArray['specialId'] .'_' .$row['title'], ''));
						}
						$add_tr->addTd($add_td);
	
						if ($row['text'] != ''){
							$add_td_3 = new tx_mailform_td();
							$add_td_3->setContent('(' .$row['text'] .')');
							$add_tr->addTd($add_td_3);
						}
	
						$add_table->addRow($add_tr);
						$table->addRow($this->makeRow($row['title'], $add_table->getElementRendered()));
					}
			}
		}
		// HOOK END
		
		$table->addRow($this->makeTitleRow("Database"));
		
		$table->addRow($this->makeRow('Database Attribute', $this->makeInputField('dbsave_attribute', $this->configData['dbsave_attribute'])));
		$table->addRow($this->makeRow('Database Tablename', $this->makeInputField('dbsave_tablename', $this->configData['dbsave_tablename'])));
		
		return $table->getElementRendered();
	}
	
	/**
	 * get FEBE Status Image
	 * Returns the URL for Wizard
	 * 
	 * @access Backend
	 *
	 * @return unknown
	 */
	protected function getFEBEStatusImage() {
		if(isset($this->configData['display_field_in_form']) && isset($this->configData['disable_field_on_email'])) {
			return '../gfx/display_no.gif';
		} elseif( isset($this->configData['display_field_in_form']) && ! isset($this->configData['disable_field_on_email'])) {
			return '../gfx/display_email.gif';
		} elseif( ! isset($this->configData['display_field_in_form']) && isset($this->configData['disable_field_on_email'])) {
			return '../gfx/display_fe.gif';
		} else {
			return '../gfx/display_feemail.gif';
		}
	}
	
	/**
	 * Enter getFEEMAIL_status
	 *
	 * @access Backend
	 * 
	 * @return state
	 */
	protected function getFEEMAIL_status() {
		global $LANG;
		
		if(isset($this->configData['display_field_in_form']) && isset($this->configData['disable_field_on_email'])) {
			$state = '<b>'.$LANG->getLL('display_FE').':</b> '.$LANG->getLL('no')."<br>".'<b>'.$LANG->getLL('display_Email').':</b> '.$LANG->getLL('no')."<br>";
		} elseif( ! isset($this->configData['display_field_in_form']) && isset($this->configData['disable_field_on_email'])) {
			$state = '<b>'.$LANG->getLL('display_FE').':</b> '.$LANG->getLL('yes')."<br>".'<b>'.$LANG->getLL('display_Email').':</b> '.$LANG->getLL('no')."<br>";
		} elseif( isset($this->configData['display_field_in_form']) && ! isset($this->configData['disable_field_on_email'])) {
			$state = '<b>'.$LANG->getLL('display_FE').':</b> '.$LANG->getLL('no')."<br>".'<b>'.$LANG->getLL('display_Email').':</b> '.$LANG->getLL('yes')."<br>";
		} else {
			$state = '<b>'.$LANG->getLL('display_FE').':</b> '.$LANG->getLL('yes')."<br>".'<b>'.$LANG->getLL('display_Email').':</b> '.$LANG->getLL('yes')."<br>";
		}

		return $state;
	}
	
	/**
	 * Render HTML, implemented in Each Fieldtype
	 *
	 * @access Backend
	 * @returns String
	 * 
	 **/
	protected abstract function renderHtml();

	protected function getStatusImageFEMAIL() {
		return '<a style="pointer:none;" onmouseover="return overlib(\''.$this->getFEEMAIL_status().'\');" onmouseout="return nd();"><img style="cursor: help;" src="'.$this->getFEBEStatusImage().'" alt="" border="0"></a>';
	}
	
	
	/**
	*
	* Backend Form Rendering
	*
	*/
	
	/**
	 * Returns a checkbox
	 *
	 *@param $name String
	 *@return String
	 */
	protected function makeCheckbox($name, $selected=false) {
		require_once(t3lib_extMgm::extPath('mailform').'lib/layout/form/class.tx_mailform_checkbox.php');
		$inputForm = new tx_mailform_checkbox();
		
		// Set Name Procedure
		$inputForm->setName(tx_mailform_funcLib::FORM_POST_PREFIX.'['.$this->configData['uName'].']['.$name.']');
		$inputForm->setChecked($this->configData[$name] == "on" || $selected);
		$inputForm->setComment('makeCheckbox($name, $selected=false)');
		
		$res .= $inputForm->getElementRendered();
		return $res;
	}
	
	/**
	 * Returns an input field
	 *
	 *@param $name String
	 *@param $defaultValue String
	 *@param $size int
	 *@return String
	 */
	protected function makeInputField($name, $defaultValue, $size = 30) {
		require_once(t3lib_extMgm::extPath('mailform').'lib/layout/form/class.tx_mailform_input.php');
		$inputForm = new tx_mailform_input();
		
		// Set Name Procedure
		$inputForm->setName(tx_mailform_funcLib::FORM_POST_PREFIX.'['.$this->configData['uName'].']['.$name.']');
		$inputForm->setSize($size);
		
		if(empty($this->configData[$name]))
			$inputForm->setValue(htmlspecialchars($defaultValue));
		else
			$inputForm->setValue(htmlspecialchars($this->configData[$name]));
		$inputForm->setComment('makeInputField($name, $defaultValue, $size = 30)');
		
		return $inputForm->getElementRendered();
	}
	
	
	/**
	 * Returns an textarea
	 *
	 *@param $name String
	 *@param $defaultValue String
	 *@param $size int
	 *@return String
	 */
	protected function makeTextarea($name, $defaultValue, $size = 30, $rows = 4) {
		require_once(t3lib_extMgm::extPath('mailform').'lib/layout/form/class.tx_mailform_textarea.php');
		$textarea = new tx_mailform_textarea();
		$textarea->setCols($size);
		$textarea->setRows($rows);
		
		$textarea->setName(tx_mailform_funcLib::FORM_POST_PREFIX.'['.$this->configData['uName'].']['.$name.']');
		
		if(empty($this->configData[$name]) || $this->configData[$name] == "") {
			// Handle Exception with HTML Element, where the HTML Element allows HTML chars
			if($this->configData['type'] == 'htmlelement')
      			$textarea->setContent($defaultValue);
      		else
      			$textarea->setContent(htmlspecialchars($defaultValue));
      		
		}
		else {
			// Handle Exception with HTML Element, where the HTML Element allows HTML chars
			if($this->configData['type'] == 'htmlelement')
      			$textarea->setContent($this->configData[$name]);
      		else
      			$textarea->setContent(htmlspecialchars($this->configData[$name]));
		}
			
		
		$textarea->setComment('makeTextarea($name, $defaultValue, $size = 30, $rows = 4)');
		$res .= $textarea->getElementRendered();
		
		return $res;
	}
	
	/**
	 * Returns an selectbox
	 *
	 * @param $name String
	 * @param $values String
	 * @param $selected String
	 * @param $size int
	 * @param $obChange
	 *      	 
	 * @return String
	 */            	
	protected function makeSelectbox($name, $values, $selected, $size=1, $onChange="") {
		require_once(t3lib_extMgm::extPath('mailform').'lib/layout/form/class.tx_mailform_select.php');
		
		$select = new tx_mailform_select();
		$select->setOnchange($onChange);
		$select->setName(tx_mailform_funcLib::FORM_POST_PREFIX.'['.$this->configData['uName'].']['.$name.']');
		$select->setComment('makeSelectbox($name, $values, $selected, $size=1, $onChange="")');
		$select->setSize($size);
		
		foreach($values as $key => $value) {
			$opt = new tx_mailform_option();
			$opt->setValue($key);
			$opt->setContent($value);
			$opt->setSelected($selected == $key);
			$select->addContent($opt);
		}
		
		return $select->getElementRendered();;
	}
	
	/**
	 * Returns a input of type hidden
	 *
	 * @param String $name
	 * @param String $value
	 * @return String
	 */
	protected function makeHidden($name, $value) {
		$hiddenField = new tx_mailform_input();
		$hiddenField->setType('hidden');
		$hiddenField->setName(tx_mailform_funcLib::FORM_POST_PREFIX.'['.$this->configData['uName'].']['.$name.']');
		$hiddenField->setValue($value);
		return $hiddenField->getElementRendered();
	}

  	/**
     * Option element for BE
     * Displays a checkbox to choose, whether the field shall be displayed
     * In the sent emails
     *
     * @return String
     */
 	protected function displayOptions() {
		global $LANG;
		return $this->makeRow( $LANG->getLL('display_on_email'), $this->makeCheckbox('disable_field_on_email') );
	}
	
	/**
	 * Returns the start of the field configuration table
	 *
	 * @return String
	 */
	protected function startRowEnv() {
		$row = new tx_mailform_tr();
		$col = new tx_mailform_td();
		$col->setColspan(2);
		$col->setHeight(0);
		$row->addTd($col);
		return $row;
	}
    
	/**
	 * Returns a formated table line for a title
	 *
	 * @param $label String
	 * @return String
	 */
	protected function makeTitleRow($label) {
		require_once(t3lib_extMgm::extPath('mailform')."lib/layout/table/class.tx_mailform_tr.php");
		$row = new tx_mailform_tr();
		$row->addCssClass('subPartTypeForm');
		
		$col = new tx_mailform_td();
		$col-> addCssClass('subPartTypeFormTitle');
		//$col->addCssClass('subPartTypeForm');
		$col->setValign('middle');
		$col->setColspan(2);
		
		$linkLabel = '<a onclick="" style="cursor:pointer">'.$label."</a>";
		
		$col->setContent($linkLabel);
		
		$row->addTd($col);
		
		$row->setComment('makeTitleRow($label)');
		return $row;
	}
	
	/**
	 * Get Selectbox with all Types
	 *
	 * @param unknown_type $fieldName
	 * @param unknown_type $selectedType
	 * @return unknown
	 */
	protected function getSelectboxOfType() {
		global $LANG;
		$selectedType = $this->configData['type'];
		
		require_once(t3lib_extMgm::extPath('mailform')."lib/layout/form/class.tx_mailform_select.php");
		
		/**
		 * Form Elements
		 */
		$selectBox = new tx_mailform_select();
		$selectBox->setName(tx_mailform_funcLib::FORM_POST_PREFIX.'['.$this->configData['uName'].'][type]');
		$selectBox->setOnchange('this.form.submit()');
		//$selectBox->setOnchange('return tx_mailform_promptChangeType()');
		
		$option = new tx_mailform_option();
		$selectBox->addContent($option);
		
		$mailforms = t3lib_div::makeInstance("tx_mailform_form");
		// select box with element type choice
		foreach($mailforms->getPossibleFormTypes() as $type) {
			if($type != 'default') {
				$option = new tx_mailform_option();
				$option->setValue($type);
				$option->setSelected($selectedType == $type);
				$option->setContent($LANG->getLL('forms_type_'.$type));
				
				$selectBox->addContent($option);
			}
		}
		
		/**
		 * Layout Types
		 */
		$option = new tx_mailform_option();
		$option->setValue('');
		$option->setContent('-- '.$LANG->getLL('layout_elements').' --');
		$selectBox->addContent($option);
		
		foreach($mailforms->getPossibleLayoutTypes() as $type) {
			if($type != 'default') {
				// Set selected, when selectedType is equal current
				$option = new tx_mailform_option();
				$option->setValue($type);
				$option->setSelected($selectedType == $type);
				$option->setContent($LANG->getLL('layout_type_'.$type));
				$selectBox->addContent($option);
			}
		}

		/**
		 * Navigation Types
		 */
		$option = new tx_mailform_option();
		$option->setValue('');
		$option->setContent('-- '.$LANG->getLL('navi_elements').' --');
		$selectBox->addContent($option);
		
		foreach($mailforms->getPossibleNaviTypes() as $type) {
			if($type != 'default') {
				// Set selected, when selectedType is equal current
				$option = new tx_mailform_option();
				$option->setValue($type);
				$option->setSelected($selectedType == $type);
				$option->setContent($LANG->getLL('navi_type_'.$type));
				$selectBox->addContent($option);
			}
		}
		
		return $selectBox->getElementRendered();
	}

	/**
	 * Returns a row with label and content
	 *
	 * @param $label String
	 * @param $formHtml String
	 * @return String
	 */
	protected function makeRow($label, $formHtml, $textAfterHtml='', $inputValid=true) {
		$formHtml = ($textAfterHtml === '') ? $formHtml : '<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr><td>'.$formHtml.'</td><td>'.$textAfterHtml.'</td></tr></table>';
	
		require_once(t3lib_extMgm::extPath('mailform')."lib/layout/table/class.tx_mailform_tr.php");
		$row = new tx_mailform_tr();
		$row->addCssClass('subPartTypeForm');
		
		$col = new tx_mailform_td();
		$col->addCssClass('subPartTypeForm_Label');
		$col->setValign(tx_mailform_attr_valign::VALIGN_MIDDLE);
		$col->setContent($label);
		$row->addTd($col);
		
		$col = new tx_mailform_td();
		$col->addCssClass('subPartTypeForm');
		$col->setValign(tx_mailform_attr_valign::VALIGN_MIDDLE);
		$col->setContent($formHtml);
		$row->addTd($col);
		$row->setComment('makeRow($label, $formHtml, $textAfterHtml=\'\'');
		return $row;
	}
	
	/**
	 * Returns an empty row
	 *
	 * @return String
	 */
	protected function DEPRECATED_makeEmptyRow() {
		$row = new tx_mailform_tr();
		
		$col = new tx_mailform_td();
		$col->setColspan(2);
		$col->setWidth('100%');
		$col->addStyle('height: 0px;');
		$row->addTd($col);
		
		return null;
	}
	
	/**
	 * Returns the ending line for the table
	 *
	 * @return String
	 */
	protected function endRowEnv() {
		$row = new tx_mailform_tr();
		$col = new tx_mailform_td();
		$col->setColspan(2);
		$col->setHeight(0);
		$row->addTd($col);
		return $row;
	}
	
	protected function makeTwoColRow($content) {
		$row = new tx_mailform_tr();
		$col = new tx_mailform_td();
		$col->setColspan(2);
		$col->setContent($content);
		$row->addTd($col);
		return $row;
	}

	/**
	 * row_preExcludeFromStats
	 *
	 * @param Boolean $selected
	 * @return String
	 */
	protected function row_preExcludeFromStats($selected=false) {
		global $LANG;
		return $this->makeRow(	$LANG->getLL('exclude_button_from_stats'),
										$this->makeCheckbox('exclude_button_from_stats', $selected) );
	}
	
	/**
	 * row_preFormButtonValue
	 *
	 * @return String
	 */
	protected function row_preFormButtonValue() {
		global $LANG;
		return $this->makeRow(	$LANG->getLL('form_button_value'),
										$this->makeInputField('form_button_value', $this->configData['form_button_value']) );
	}
	
	/**
	 * row_preFormButtonValue
	 *
	 * @return String
	 */
	protected function row_preFormStandardValue() {
		global $LANG;
		return $this->makeRow(	$LANG->getLL('form_standard_value'),
										$this->makeInputField('input_field_value', $this->input_field_value) );
	}
	
	/**
	 * row_preFormButtonValue
	 *
	 * @return String
	 */
	protected function row_preFormDescription() {
		global $LANG;
		return $this->makeRow(	$LANG->getLL('input_textarea_desc'),
										$this->makeTextarea('input_textarea_desc', $this->input_textarea_desc) );
	}

	/**
	 * row_preFormButtonValue
	 *
	 * @return String
	 */
	protected function row_PreHtmlEditor() {
		require_once(t3lib_extMgm::extPath('mailform')."lib/class.tx_mailform_processInput.php");
		$processInput = new tx_mailform_processInput();
		
		global $LANG;
		return $this->makeRow(	$LANG->getLL('input_html_value'),
										$this->makeTextarea('input_html_value', $this->input_html_value, 65, 20)
									);
	}
	
	/**
	 * Add a Required Line
	 *
	 * @return Array
	 */
	protected function row_preRequiredSpecValue() {
		global $LANG;

		$arr = array();
		if(isset($this->configData['required']) && $this->configData['required'] == 'on')
		$arr[] = $this->makeRow($LANG->getLL('forms_required_nopass_value'), $this->makeInputField('forms_required_nopass_value', $this->configData['forms_required_nopass_value'])."(".$LANG->getLL('only_when_required').")");
		
		return $arr;
	}

	/**
	 * row_preFormButtonValue
	 *
	 * @return String
	 */
	protected function row_PreShowIfContainsValue() {
		global $LANG;
		return $this->makeRow(	$LANG->getLL('input_contains_value'),
										$this->makeCheckbox('input_contains_value') );
	}
	
	/**
	 * row_preFormButtonValue
	 *
	 * @return String
	 */
	protected function row_preDisplayFieldInForm() {
		global $LANG;
		return $this->makeRow(	$LANG->getLL('display_field_in_form'),
										$this->makeCheckbox('display_field_in_form') );
	}
	
	protected function row_preDisplayFieldCondition() {
		global $LANG;
			$cbBoolSelected = ($this->configData['display_field_condition_active'] == "on");
		$cbSelected =  $cbBoolSelected ? ' checked' : '';
		$name = tx_mailform_funcLib::FORM_POST_PREFIX.'['.$this->configData['uName'].'][display_field_condition_active]';
		$cb = '<input type="checkbox" onchange="changeVisibility(\'display_field_condition_div\')" name="'.$name.'" '.$cbSelected.'/>';
		
		$display = ($cbBoolSelected) ? 'display:block;' : 'display:none;';

		$name = tx_mailform_funcLib::FORM_POST_PREFIX.'['.$this->configData['uName'].'][display_field_condition]';
		
		if($this->configData['display_field_condition'] == '') {
			$this->configData['display_field_condition'] = "(1)";
		}
		
		$textArea = '<div style="'.$display.'" id="display_field_condition_div"><textarea name="'.$name.'" cols="60" rows="4">'.$this->configData['display_field_condition'].'</textarea></div>';
		
		return $this->makeRow( $LANG->getLL('display_field_condition'), $cb.$textArea);
	}

	/**
	 * splitElements($csv)
	 *
	 * @return Array;
	 */
	protected function splitElements($csv) {
  		$arr = split(",", $csv);
  		$h = array();
  		foreach($arr as $a) {
  			if($a != "")
  				$h[] = $a;
  		}
  		return $h;
  	}
}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/class.tx_mailform_formAbstract_BE.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/class.tx_mailform_formAbstract_BE.php']);
}
?>