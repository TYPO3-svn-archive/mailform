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

require_once(t3lib_extMgm::extPath('mailform')."lib/class.tx_mailform_urlHandler.php");
require_once(t3lib_extMgm::extPath('mailform')."formTypesModel/class.tx_mailform_form.php");

class tx_mailform_fieldWiz {
	
	private $urlHandler;
	private $urlKeys;
	private $wizardReference;
	
	public static $GET_fieldVars = array('newField', 'listFields', 'edtField', 'delItemFromList', 'delFfromF');
	
	public function __construct($wizardReference) {
		$gpPost = t3lib_div::_POST();
		if(isset($gpPost[tx_mailform_funcLib::FORM_POST_PREFIX])) {
			$this->saveFieldToConfigData();
		}
	
		$this->urlHandler = new tx_mailform_urlHandler();
		$this->urlKeys = array_merge(tx_mailform_wizard::$GET_vars, tx_mailform_fieldWiz::$GET_fieldVars);
		$this->wizardReference = $wizardReference;
		$this->displayFunctions();
	}
	
	
	/**
	 * This method saves all done changes sides user
	 * It does reload all configuration from the wizard too
	 *
	 */
	private function displayFunctions() {
		// Delete Form if selected on list to delete
		if(isset($_GET['delItemFromList'])) {
			$tmFH = tx_mailform_formHandler::getInstance();
			$tmFH->removeForm($_GET['delItemFromList']);
		}
	}
	
	public function getElementRendered() {
		global $LANG;

		//throw new Exception('test');
		$tmfH = tx_mailform_formHandler::getInstance();
		return '
		<table cellpadding="0" cellspacing="0" border="0" summary="none" id="fieldWizard-table">
			<tr><td style="height:20px;"></td></tr>
			<tr>
				<td class="fieldWizard-title">
					<table summary="" width="100%" cellpadding="0" cellspacing="0"><tr>
						<td id="fieldWizard-title_text"><a name="fieldWizard"></a>'.$LANG->getLL('fWiz_field_wizard').'</td>
						<td align="right" style="padding-right: 3px; padding-top: 3px;">
							<a href="'.$this->urlHandler->getCurrentUrl(array_merge($this->urlKeys, array('edtField'))).'&amp;newField='.$tmfH->getUniqueFieldname().'"><img src="../gfx/new_el.gif" alt="'.$LANG->getLL('fWiz_new_field').'" title="'.$LANG->getLL('fWiz_new_field').'"></a>
							<a href="'.$this->urlHandler->getCurrentUrl(array_merge($this->urlKeys, array('edtField', 'listFields'))).'&amp;listFields"><img class="fWiz_titleLink_img" src="../gfx/multi_documents.png" alt="'.$LANG->getLL('fWiz_list_field').'" title="'.$LANG->getLL('fWiz_list_field').'"></a>
						</td>
					</tr></table>
				</td>
			</tr>
			<tr>
				<td id="fieldWizard-content">'.$this->getContent().'</td>
			</tr>
		</table>
		';
	}
	
	private function getContent() {
		if(isset($_GET['newField']))
			return $this->newField();
		if(isset($_GET['listFields']))
			return $this->getFieldlist();
		if(isset($_GET['edtField']))
			return $this->getEditField();
		return $this->getEmptyWizard();
	}
	
	private function newField() {
		global $cObj, $LANG;
		$tmfH = tx_mailform_formHandler::getInstance();
		$newForm = new tx_mailform_form();
		$newForm->setupForm(array('uName' => $tmfH->getUniqueFieldname()), '', $cObj);

		$res = '
		<table cellpadding="0" cellspacing="0" width="100%" border="0" summary="none">
			<tr class="subPartTypeForm">
				<td style="width: 100%;" valign="top">
					'.$newForm->getForm()->getHtml().'
				</td>
			</tr>
			<tr><td><input type="submit" value="'.$LANG->getLL('fWiz_create_form').'" /></td></tr>
		</table>
		</form>
		';
		
		return $res;
	}
	
	private function getEditField() {
		global $LANG;
		$tmfH = tx_mailform_formHandler::getInstance();
		try {			
			$editForm = $tmfH->getForm($_GET['edtField']);
			$editForm = $editForm->getForm()->getHtml();
			
		} catch (Exception $e) {
			$editForm = $LANG->getLL('fWiz_formular_not_found');
		}
		
		$res = '
		<table cellpadding="0" cellspacing="0" width="100%" border="0" summary="none">
			<tr class="subPartTypeForm">
				<td style="width: 100%;" valign="top">
					'.$editForm.'
				</td>
			</tr>
			<tr><td><input type="submit" value="Update Form" /></td></tr>
		</table>
		</form>
		';
		
		return $res;
	}
	
	private function getEmptyWizard() {
		return '';
	}
	
	private function getFieldList()	{
		global $LANG;
		require_once(t3lib_extMgm::extPath('mailform')."lib/layout/table/class.tx_mailform_table.php");
		require_once(t3lib_extMgm::extPath('mailform')."lib/layout/form/class.tx_mailform_input.php");
		$table = new tx_mailform_table();
		$table->setWidth("100%");
		$table->addCssClass('asfasdf');
		$table->setCellpadding(0);
		$table->setCellspacing(0);
		$table->setBorder(false);
		$table->setComment('getBE_preview of Field()');
		
		$tmfH = tx_mailform_formHandler::getInstance();
		$formElements = $tmfH->getForms();
		
		$table = new tx_mailform_table();
		$table->setWidth("100%");
		$table->addCssClass('subPartTypeForm');
		$table->setCellpadding(0);
		$table->setCellspacing(0);
		$table->setBorder(false);
		$table->setComment('List Title Comment');
		
		// List Header
		$row = new tx_mailform_tr();
		$col = new tx_mailform_td();
		$col->addCssClass('subPartTypeFormTitle');
		$col->setContent($LANG->getLL('info_label_key'));
		$col->setWidth('140');
		$col->setColspan(2);
		$row->addTd($col);
			
		$col = new tx_mailform_td();
		$col->addCssClass('subPartTypeFormTitle');
		$col->setContent($LANG->getLL('forms_label'));
		$row->addTd($col);
			
		$col = new tx_mailform_td();
		$col->addCssClass('subPartTypeFormTitle');
		$col->setContent($LANG->getLL('forms_element'));
		$row->addTd($col);

		$col = new tx_mailform_td();
		$col->addCssClass('subPartTypeFormTitle');
		$col->setContent("");
		$col->setColspan(2);
		$row->addTd($col);
		
		$table->addRow($row);

		foreach($formElements as $pIndex => $page) {
			foreach($page as $fIndex => $formElement) {
				$table = $formElement->getForm()->getBE_Preview($table);
			}
		}
		
		return $table->getElementRendered();
	}
	
	private function saveFieldToConfigData() {
		$tmfH = tx_mailform_formHandler::getInstance();
		
		$gpPost = t3lib_div::_POST();
		
		$tmfH->addForm($gpPost[tx_mailform_funcLib::FORM_POST_PREFIX]['uName']);
		
		$formConfig['uName'] = $gpPost[tx_mailform_funcLib::FORM_POST_PREFIX]['uName'];
		foreach($gpPost[tx_mailform_funcLib::FORM_POST_PREFIX][$gpPost[tx_mailform_funcLib::FORM_POST_PREFIX]['uName']] as $key => $fieldContent) {
			$formConfig[$key] = $fieldContent;
		}

		$tmfH->loadFormConfig($gpPost[tx_mailform_funcLib::FORM_POST_PREFIX]['uName'], $formConfig);
		$tmfH->loadConfigData();

		$state = tx_mailform_saveState::getInstance();
		$state->setChanged(true);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/tt_content_tx_mailform_config/model/class.tx_mailform_fieldWiz.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/tt_content_tx_mailform_config/model/class.tx_mailform_fieldWiz.phpp']);
}
?>