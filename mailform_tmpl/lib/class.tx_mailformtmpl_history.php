<?
require_once(t3lib_extMgm::extPath('mailform_tmpl')."lib/class.tx_mailformtmpl_historyObj.php");
class tx_mailformtmpl_history {
	
	private $history_enabled;
	private $history_count;
	private $history_objects = array();
	private static $instance;
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new tx_mailformtmpl_history();
		}
		return self::$instance;
	}
	
	
	private function __construct() {
		$settings = tx_mailformtmpl_settings::getInstance();
		$this->history_enabled = tx_mailformtmpl_settings::getVariable('SAVE_HISTORY') ? true : false;
		$this->history_count = intval(tx_mailformtmpl_settings::getVariable('NR_SAVINGS'));
		$this->loadHistory();
	}
	
	private function loadHistory() {	
		if($this->history_enabled) {
			$P = t3lib_div::_GP('P');
			$sql = "SELECT * FROM tx_mailformtmpl_history WHERE plid = '".$P['uid']."' ORDER BY saved DESC";
			$res = $GLOBALS['TYPO3_DB']->sql_query($sql);
			$this->history_objects = array();
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$obj = new tx_mailformtmpl_historyObj();
				$obj->loadDBassocRow($row);
				$this->history_objects[] = $obj;
			}
		}
	}
	
	public function getHistoryObjects() {
		return $this->history_objects;
	}
	
	public function getDisplay() {
		global $LANG;
		if($this->history_enabled) {
			$table = new tx_mailform_table();
			$table->addStyle('width: 100%;');
			$table->setCellspacing(0);
			$table->setCellpadding(2);
			
			$styleParam = 'border-bottom: 1px solid #8fb5d6; font-weight: bold;';
			$row = new tx_mailform_tr();
			$c1 = new tx_mailform_td();
			$c1->addStyle($styleParam);
			$c1->setContent($LANG->getLL('history_backup_date'));
			$c2 = new tx_mailform_td();
			$c2->addStyle($styleParam);
			$c2->setContent("&nbsp;");
			$row->addTd($c1);
			$row->addTd($c2);
			$table->addRow($row);
			foreach($this->history_objects as $obj) {
				$table->addRow($obj->getDisplay());
			}
			return $table;
		}
		else {
			$table = new tx_mailform_table();
			$c1 = new tx_mailform_td();
			$tr = new tx_mailform_tr();
			$tr->addTd($c1);
			$table->addRow($tr);
			return $table;
		}
	}
	
	public function saveHistory() {
		if($this->history_enabled) {
			$P = t3lib_div::_GP('P');
			$sql = "SELECT count(uid) FROM tx_mailformtmpl_history WHERE plid = '".$P['uid']."'";
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_row($GLOBALS['TYPO3_DB']->sql_query($sql));

			$cfData = tx_mailform_configData::getInstance();
			
			$xml = $cfData->getCompleteXML();
			$xml = str_replace("'", "&#39;", $xml);
			//$xml = $cfData->getCompleteXML();

			if($row[0] >= $this->history_count) {
				if($row[0] > $this->history_count) {
					$sql = "SELECT uid, saved, plid FROM tx_mailformtmpl_history WHERE plid = '".$P['uid']."' ORDER BY saved DESC";
					$res = $GLOBALS['TYPO3_DB']->sql_query($sql);

					$entries = array();
					while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
						$entries[] = $row;
					}
					
					while(sizeof($entries) > $this->history_count) {
						$sql = "DELETE FROM tx_mailformtmpl_history WHERE uid = '".$entries[sizeof($entries)-1]['uid']."'";

						$GLOBALS['TYPO3_DB']->sql_query($sql);
						unset($entries[sizeof($entries)-1]);
					}
				}				
				$sql = "UPDATE tx_mailformtmpl_history SET saved = '".time()."', xml_element = '".$xml."' WHERE saved = (SELECT MIN(saved)) AND plid = '".$P['uid']."'";
				$GLOBALS['TYPO3_DB']->sql_query($sql);
				
			} else {
				$sql = "INSERT INTO tx_mailformtmpl_history (plid,saved,xml_element) VALUES ('".$P['uid']."', '".time()."', '".$xml."')";
				$GLOBALS['TYPO3_DB']->sql_query($sql);
				
			}
			$this->loadHistory();
		}

	}
}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform_tmpl/lib/class.tx_mailformtmpl_history.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform_tmpl/lib/class.tx_mailformtmpl_history.php']);
}
?>