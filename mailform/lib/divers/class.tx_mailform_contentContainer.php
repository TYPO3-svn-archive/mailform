<?php
class tx_mailform_contentContainer {
	
	private $content = null;
	
	public function __construct($content) {
		
	}
	
	public function setContent($content) {
		if(gettype($content) == 'object') {
			if($content instanceof tx_mailform_parent)
				$this->content = $content;
		}
	}
	
	public function __toString() {
		
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/divers/class.tx_mailform_contentContainer.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/divers/class.tx_mailform_contentContainer.php']);
}
?>