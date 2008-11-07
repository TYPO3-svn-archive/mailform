<?php

interface tx_mailform_I_multipleContent {
	public function addContent($content);
	public function getContent();
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/layout/interface/interface.tx_mailform_I_multipleContent.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/layout/interface/interface.tx_mailform_I_multipleContent.php']);
}
?>