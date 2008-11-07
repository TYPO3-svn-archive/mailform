<?php
interface tx_mailform_observable {
	public function update();
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/controller/class.tx_mailform_observable.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/controller/class.tx_mailform_observable.php']);
}
?>