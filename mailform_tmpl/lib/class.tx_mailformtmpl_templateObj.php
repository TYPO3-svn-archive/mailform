<?

require_once(t3lib_extMgm::extPath('mailform')."lib/class.tx_mailform_processInput.php");

class tx_mailformtmpl_templateObj {

	private $xml;
	private $xml_title;
	private $xml_author;
	private $xml_date;
	private $xml_description;
	private $xml_uid;
	private $savePath;
	private $certified = false;

	public function __construct() {
		$this->xml_date = time();
	}

	public function getConfigDataXML() {
		return $this->xml;
	}

	public function saveXMLinFile() {
		$loader = tx_mailformtmpl_loader::getInstance();
		$loader->saveXMLinFile($this);
	}

	public function loadXML($xml) {
		$arr = t3lib_div::xml2array($xml);

		if(!is_array($arr))
			throw new Exception("XML Is not valid");

		$this->setTitle($arr['xml_title']);
		$this->setAuthor($arr['xml_author']);

		$this->setDate($arr['xml_date']);
		$this->setDescription($arr['xml_description']);

		$this->setUid($arr['xml_uid']);
		$this->xml = $arr['xml'];
	}

	public function setConfigXML($xml) {
		$this->xml = $xml;
	}

	public function getConfigXML($key) {
		return $this->xml[$key];
	}

	public function getFilename($copy=false) {
		require_once(t3lib_extMgm::extPath('mailform')."lib/class.tx_mailform_processInput.php");
		$PI = new tx_mailform_processInput();
		$copyCount = $copy !== false ? "($copy)" : "";
		$filename = date('dmy', $this->xml_date)."_".$PI->short_without_html($this->xml_title, 10)."_".$PI->short_without_html($this->xml_author, 10).$copyCount.".xml";
		return $filename;
	}

	public function getFileXMLArray() {
		$fileXml = array (
			'xml_title' => $this->xml_title,
			'xml_author' => $this->xml_author,
			'xml_date' => $this->xml_date,
			'xml_description' => $this->xml_description,
			'xml' => $this->xml,
			'xml_uid' => $this->xml_uid
		);
		return $fileXml;
	}

	public function setTitle($value) {
		$this->xml_title = $value;
	}

	public function getTitle() {
		return $this->xml_title;
	}

	public function setUid($int) {
		if($int == "")
			throw new Exception("UNIQUE ID IS EMPTY");
		$this->xml_uid = $int;
	}

	public function getUid() {
		return $this->xml_uid;
	}

	public function setAuthor($value) {
		$this->xml_author = $value;
	}

	public function getAuthor() {
		return $this->xml_author;
	}

	public function setDescription($value) {
		$this->xml_description = $value;
	}

	public function getDescription() {
		return $this->xml_description;
	}

	public function setDate($value) {
		$this->xml_date = $value;
	}
	
	public function setDir($dir) {
	  if(file_exists($dir))
		$this->savePath = $dir;
		else
		  throw new Exception('Give Path for saving the xml is invalid: '.$dir);
	}
	
	public function getDir() {
		return $this->savePath;
	}

	public function getDate() {
		if($this->xml_date == '')
			return 0;
		return intval($this->xml_date);
	}

	public function getDisplay() {
		global $LANG;
		$urlHandler= new tx_mailform_urlHandler();
		$row = new tx_mailform_tr();
		$c1 = new tx_mailform_td();
		$c1->setContent($this->getAuthor());
		$row->addTd($c1);

		$c2 = new tx_mailform_td();
		$c2->setContent($this->getTitle());
		$row->addTd($c2);

		$PI = new tx_mailform_processInput();
		$c3 = new tx_mailform_td();
		$c3->setContent($PI->short_without_html($this->getDescription(),50));
		$row->addTd($c3);

		/*
		$c5 = new tx_mailform_td();
		$c5->addStyle('');
		$c5->setContent($this->g);
		$row->addTd($c5)
		*/
		
		$c4 = new tx_mailform_td();
		$c4->addStyle('font-weight:bold;');
		$c4->setContent(date('d.m.Y', $this->getDate()));
		$row->addTd($c4);

    

		$clink = new tx_mailform_td();
		$clink->addStyle('text-align: right;');
		$link = '<input type="image" src="'.tx_mailform_parentWizard::getRelativePath('mailform_tmpl').'gfx/arrow_right_up.gif" onclick="return confirm(\''.$LANG->getLL('confirm_change_xml').'\')">';

		$hidden = new tx_mailform_input();
		$hidden->setType('hidden');
		$hidden->setName('mftmpl_repl');
		$hidden->setValue($this->getUid());

		$clink->setContent("<form action=\"\" method=\"post\">".$hidden->getElementRendered().$link."</form>");

		$clink->addStyle('border: 0px none #FFF;');
		$row->addTd($clink);

		return $row;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform_tmpl/lib/class.tx_mailformtmpl_templateObj.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform_tmpl/lib/class.tx_mailformtmpl_templateObj.php']);
}
?>
