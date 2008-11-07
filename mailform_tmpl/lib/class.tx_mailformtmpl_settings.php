<?
session_start();
class tx_mailformtmpl_settings {

	private static $instance;
	public static $settingsArray = array (
											'SAVE_HISTORY' => 0,
											'NR_SAVINGS' => 10,
											'XML_USER' => '../../../../fileadmin/ext/mailform/mailform_tmpl/',
											'XML_STANDARD' => 'EXT:mailform_tmpl/xml_templates/',
											
										);
	public static $defautlArray = array (
											'SAVE_HISTORY' => 0,
											'NR_SAVINGS' => 10,
											'XML_USER' => '../../../../fileadmin/ext/mailform/mailform_tmpl/',
											'XML_STANDARD' => 'EXT:mailform_tmpl/xml_templates/',
											
										);
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new tx_mailformtmpl_settings();
		}
		return self::$instance;
	}

	/**
	 *
	 *
	 *
	 */
	private function __construct() {
		if(!isset($_SESSION['tx_mailformtmpl_settings']))
			$this->loadSettings();
		else {
			tx_mailformtmpl_settings::$settingsArray = $_SESSION['tx_mailformtmpl_settings'];
		}
	}

	private static function loadSettings() {
		$sql = "SELECT * FROM tx_mailformtmpl_settings";
	    $res = $GLOBALS['TYPO3_DB']->sql_query($sql);
	    while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
	    	if(array_search($row['settings_key'], array_keys(tx_mailformtmpl_settings::$settingsArray)) !== false) {
	    		tx_mailformtmpl_settings::$settingsArray[$row['settings_key']] = $row['settings_value'];
	    	}
	    }
	    $_SESSION['tx_mailformtmpl_settings'] = tx_mailformtmpl_settings::$settingsArray;
	}

	public static function getVariable($key) {
		if(isset($_SESSION['tx_mailformtmpl_settings'])) {
			return $_SESSION['tx_mailformtmpl_settings'][$key];
		} else {
			return tx_mailformtmpl_settings::$settingsArray[$key];
		}
	}

	/**
	 *  saveVariable($key, $value)
	 *
	 * @return void
	 */
	public static function saveVariable($key, $value) {
		if(array_search($key, array_keys(tx_mailformtmpl_settings::$settingsArray)) === false) {
			throw new Exception('Key "'.$key.'" is not available in Array, please adapt settings.php');
		}

		$sql = "SELECT * FROM tx_mailformtmpl_settings WHERE settings_key = '".$key."'";
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($GLOBALS['TYPO3_DB']->sql_query($sql));
		if(empty($row)) {
			$sql = "INSERT INTO tx_mailformtmpl_settings (settings_key, settings_value) VALUES ('".$key."','".$value."')";
			$GLOBALS['TYPO3_DB']->sql_query($sql);
		} else {
			$sql = "UPDATE tx_mailformtmpl_settings SET settings_value='".$value."' WHERE settings_key = '".$key."'";
			$GLOBALS['TYPO3_DB']->sql_query($sql);
		}
		tx_mailformtmpl_settings::loadSettings();
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform_tmpl/lib/class.tx_mailformtmpl_settings.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform_tmpl/lib/class.tx_mailformtmpl_settings.php']);
}
?>
