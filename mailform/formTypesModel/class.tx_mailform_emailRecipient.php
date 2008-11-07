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
require_once(t3lib_extMgm::extPath("mailform")."/formTypesModel/class.tx_mailform_formValidation.php");
require_once(t3lib_extMgm::extPath('mailform')."/lib/layout/form/class.tx_mailform_select.php");
require_once(t3lib_extMgm::extPath('mailform')."/lib/layout/form/class.tx_mailform_option.php");

/**
* mailform module tt_content_tx_mailform_forms
*
* @to add an additional form type, register the form type in tx_mailform_form.
*
* @author       Sebastian Winterhalder <sw@internetgalerie.ch>
* 
*/
abstract class tx_mailform_emailRecipient extends tx_mailform_formValidation {

  private $validationTypes = array('no_validation', 'mail', 'alphanum', 'numbers', 'letters', 'regex_input', 'value_check');

  /**
   *
   * Get Validation HTML
   *
   */
  protected function getEmailRecipientHtml() {
    global $LANG;
    
    foreach($this->validationTypes as $types) {
      $array[$types] = $LANG->getLL('validation_'.$types);
    }

	$selectBox = new tx_mailform_select();
	$selectBox->setName(tx_mailform_funcLib::FORM_POST_PREFIX.'['.$this->configData['uName'].'][use_as_email_choose]');
	$selectBox->setOnchange('this.form.submit()');
	//$selectBox->setOnchange('return tx_mailform_promptChangeType()');
	
	$option = new tx_mailform_option();
	$option->setContent($LANG->getLL('email_no_recipient'));
	$option->setValue('no_recipient');
	if($this->configData['use_as_email_choose'] == 'no_recipient')
		$option->setSelected(true);
	$selectBox->addContent($option);
    
	$option = new tx_mailform_option();
	$option->setContent($LANG->getLL('email_admin_recipient'));
	$option->setValue('admin_recipient');
	if($this->configData['use_as_email_choose'] == 'admin_recipient')
		$option->setSelected(true);
	$selectBox->addContent($option);
	
	$option = new tx_mailform_option();
	$option->setContent($LANG->getLL('email_user_recipient'));
	$option->setValue('user_recipient');
	if(isset($this->configData['use_as_email']) || $this->configData['use_as_email_choose'] == 'user_recipient')
		$option->setSelected(true);
	$selectBox->addContent($option);
	
	$option = new tx_mailform_option();
	$option->setContent($LANG->getLL('email_all_recipient'));
	$option->setValue('all_recipient');
	if($this->configData['use_as_email_choose'] == 'all_recipient')
		$option->setSelected(true);
	$selectBox->addContent($option);

    $array = array();
    $array[] = $this->makeTitleRow($LANG->getLL('email_recipient'));
   // $array[] = $this->startRowEnv();
   
   // $this->makeCheckbox('use_as_email')
    $array[] = $this->makeRow($LANG->getLL('email_recipient_check_label'), $selectBox->getElementRendered());
   // $array[] = tx_mailform_formAbstract::endRowEnv();
    return $array;
  }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/class.tx_mailform_formValidation.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/class.tx_mailform_formValidation.php']);
}
?>