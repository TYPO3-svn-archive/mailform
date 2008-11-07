<?

class tx_mailformtmpl_loader {

	private static $instance;
	private $templateList = array();
	private $xml_dir = array();
	public $highest_uid = 0;

	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new tx_mailformtmpl_loader();
		}
		return self::$instance;
	}

	public function saveXMLinFile($templateObject) {
		$files = scandir($templateObject->getDir());

		$filename = $templateObject->getDir().$templateObject->getFilename();
		
		$c = 1;
		while(file_exists($filename)) {
		  $filename = $templateObject->getDir().$templateObject->getFilename($c);
			$c++;
		}

		$res = fopen($filename, 'w+');
		fwrite($res, t3lib_div::array2xml($templateObject->getFileXMLArray()));
		fclose($res);
	}

	private function __construct() {
		$tSetting = tx_mailformtmpl_settings::getInstance();

		try {
			$this->xml_dir['XML_STANDARD'] = tx_mailform_funcLib::parseExtPath($tSetting->getVariable('XML_STANDARD'));
		} catch (Exception $e) { $this->xml_dir['XML_STANDARD'] = false; }
		
		try {
      $this->xml_dir['XML_USER']  = tx_mailform_funcLib::parseExtPath($tSetting->getVariable('XML_USER'));
		} catch (Exception $e) { $this->xml_dir['XML_USER'] = false; }
	
		$this->loadTemplates();
	}

	private function loadTemplates() {
		$this->templateList = array_merge($this->templateList, $this->getTemplatesList($this->xml_dir['XML_STANDARD']));
		$this->templateList = array_merge($this->templateList, $this->getTemplatesList($this->xml_dir['XML_USER']));
	}

	private function getTemplatesList($path) {
		$templateList = array();

		if($path != false && !empty($path)) {
		  if(file_exists($path) && is_dir($path))
				$array = scandir($path);
			else {
				$array = array();
			}
		}
		else
		  $array = array();

		foreach($array as $filename) {
			if($filename != '.' && $filename != '..') {
				$filename = $path.$filename;
				$res = fopen($filename, 'r');

				$xml = fread($res, filesize($filename));
				fclose($res);

				$obj = new tx_mailformtmpl_templateObj();
				try {
					$obj->loadXML($xml);

					//Determine highest uid for autoincrement uid when creating a new one
					if($obj->getUid() > $this->highest_uid) {
						$this->highest_uid = $obj->getUid();
					}

					$templateList[] = $obj;
				} catch (Exception $e) { }
			}
		}
		return $templateList;
	}


	public function replaceTemplateWith($uid) {
		$cfData = tx_mailform_configData::getInstance();
		foreach($this->templateList as $obj) {
			if($obj->getUid() == $uid) {
				$obj;
				break;
			}
		}

		$cfData->setConfigData($obj->getConfigXML('mailform_forms'));
		$cfData->setFieldData($obj->getConfigXML('mailform_config'));
		$ss = tx_mailform_saveState::getInstance();
		$ss->setChanged(true);
	}

	public function replaceTemplateWithHistory($historyObj) {
		$arr = t3lib_div::xml2array($historyObj->getXml());

		$cfData = tx_mailform_configData::getInstance();
		$cfData->setConfigData($arr['mailform_forms']);
		$cfData->setFieldData($arr['mailform_config']);
		$ss = tx_mailform_saveState::getInstance();
		$ss->setChanged(true);
	}

	public function getTemplates() {
		return $this->templateList;
	}

	/**
	 * getTemplatePath();
	 *
	 * @return Array
	 */
	public function getTemplatePath() {
	  return $this->xml_dir;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform_tmpl/lib/class.tx_mailformtmpl_loader.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform_tmpl/lib/class.tx_mailformtmpl_loader.php']);
}
?>
