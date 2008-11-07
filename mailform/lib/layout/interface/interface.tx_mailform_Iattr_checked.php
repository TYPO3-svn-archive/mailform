<?php
require_once(t3lib_extMgm::extPath('mailform')."lib/layout/attributes/class.tx_mailform_attr_checked.php");
interface tx_mailform_Iattr_checked {
	public function setChecked($boolean);
	public function getChecked();
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/layout/interface/interface.tx_mailform_Iattr_checked.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/layout/interface/interface.tx_mailform_Iattr_checked.php']);
}
?>