<?php
class tx_mailform_observer {
	
	private $Observer_observables = array();
	
	public function updateObservables() {
		foreach($this->Observer_observables as $observer) {
			$observer->update();
		}
	}
	
	public function addObservable($observable) {
		if(!gettype($observable) == "object")
			throw new Exception('Given argument is invalid. Should be an Observable object');
		$this->Observer_observables[] = $observable;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/controller/class.tx_mailform_observer.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/controller/class.tx_mailform_observer.php']);
}
?>