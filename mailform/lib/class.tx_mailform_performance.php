<?php
/**
 * Class DEBUG
 *
 */
class tx_mailform_performance
{
	private $startzeit  = 0;
	private $serverzeit = 0;
	private $serverload = 0;
	
	function time_take($save = 1)
	{
		list($msec, $sec) = explode(" ",microtime());
		
		$zeit = ((float)$msec + (float)$sec);
		
		if($save)
		{
			$this->startzeit = $zeit;
		}
		
		return $zeit;
	}
	function time_finish()
	{
		$this->serverzeit = round((($this->time_take(0) - $this->startzeit)), 3);

		
		while(strlen($this->serverzeit) < 5)
		{
			$this->serverzeit = $this->serverzeit."0";
		}
		$this->serverzeit = str_replace("00000", "0.000", $this->serverzeit);
		
		return $this->serverzeit;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/class.tx_mailform_performance.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/class.tx_mailform_performance.php']);
}

?>