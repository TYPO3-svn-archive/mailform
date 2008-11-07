<?

require_once(t3lib_extMgm::extPath('mailform')."lib/class.tx_mailform_processInput.php");

class tx_mailformtmpl_historyObj {

	private $history_date;
	public $history_plid;
	private $db_uid;
	private $xml;

	public function __construct() {
	}
	
	
	public function loadDBassocRow($array) {
		$this->history_date = $array['saved'];
		$this->history_plid = $array['plid'];
		$this->db_uid = $array['uid'];
		$this->xml = $array['xml_element'];
	}
	
	public function getDisplay() {
		global $LANG;
		$row = new tx_mailform_tr();
		$c1 = new tx_mailform_td();
		$c2 = new tx_mailform_td();
		$c2->addStyle('text-align: right;');
		$c1->setContent("XML: ".date('d.m.Y - h:i:s', $this->history_date));
		$urlHandler = new tx_mailform_urlHandler();
		$link = '<input type="image" src="'.tx_mailform_parentWizard::getRelativePath('mailform_tmpl').'gfx/arrow_right_up.gif" onclick="return confirm(\''.$LANG->getLL('confirm_change_xml').'\')">';
		
		$hidden = new tx_mailform_input();
		$hidden->setType('hidden');
		$hidden->setName('mftmpl_history');
		$hidden->setValue($this->db_uid);
		
		$c2->setContent('<a href="'.$urlHandler->getCurrentUrl(tx_mailformtmpl::$critical_getVars).'" onclick="return confirm(\''.$LANG->getLL('confirm_change_xml').'\')"><img src="'.tx_mailform_parentWizard::getRelativePath('mailform_tmpl').'gfx/arrow_right_up.gif"></a>');
		$c2->setContent("<form action=\"\" method=\"post\">".$hidden->getElementRendered().$link."</form>");
		
		$row->addTd($c1);
		$row->addTd($c2);
		return $row;
	}
	
	public function setupTemplateInWizard() {
		$loader = tx_mailformtmpl_loader::getInstance();
		$loader->replaceTemplateWithHistory($this);
	}
	
	public function getUid() {
		return $this->db_uid;
	}
	
	public function getXml() {
		return $this->xml;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform_tmpl/lib/class.tx_mailformtmpl_historyObj.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform_tmpl/lib/class.tx_mailformtmpl_historyObj.php']);
}
?>