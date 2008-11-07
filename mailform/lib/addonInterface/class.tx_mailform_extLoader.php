<?php

class tx_mailform_extLoader {
	
	private static $instance;
	
	private $addonList = array();
	
	private function __construct() {
		global $GLOBALS;
		$this->generateAddonList();
	}
	
	private function generateAddonList() {
		global $GLOBALS;
		foreach($GLOBALS['TYPO3_LOADED_EXT'] as $extKey => $extArray) {
			// Check names first
			
			preg_match('(mailform_[a-zA-Z0-9_\-]{1,})', $extKey, $treffer);
			if(count($treffer) > 0) {
				if(t3lib_extMgm::isLoaded($extKey)) {
					$shortened_name = str_replace("_", "", $extKey);
					
					$clName = "tx_".$extKey;
					$path = t3lib_extMgm::extPath($extKey)."class.tx_".$extKey.".php";
					if(!file_exists($path)) {
						$clName = "tx_".$extKey;
						$path = t3lib_extMgm::extPath($extKey)."class.".$extKey.".php";
						if(!file_exists($path)) {
							$clName = $extKey;
							$path = t3lib_extMgm::extPath($extKey).$extKey.".php";
							if(!file_exists($path)) {
								$clName = "tx_".$shortened_name;
								$path = t3lib_extMgm::extPath($extKey)."class.tx_".$shortened_name.".php";
								if(!file_exists($path)) {
									$clName = $shortened_name;
									$path = t3lib_extMgm::extPath($extKey)."class.".$shortened_name.".php";
									if(!file_exists($path)) {
										$clName = $shortened_name;
										$path = t3lib_extMgm::extPath($extKey).$shortened_name.".php";
										if(!file_exists($path)) {
											die("Addon: $ext_key did not find class to include: $path. The Class name must be the same as the Filename");
										}
									}
								}
							}
						}
					}

					require_once($path);
					$instance = t3lib_div::makeInstance($clName);
					
					if($instance instanceof tx_mailform_FE_Addon ) {
						$this->addAddon($instance, $extKey, 'FE');
					}
					
					if($instance instanceof tx_mailform_BE_Addon) {
						$this->addAddon($instance, $extKey, 'BE');
					}
					/*if(!file_exists($path)){
						print "did not find class";
						$clName = $shortened_name;
					} else
						$clName = $extKey;
					
					print $clName."<br>";
						*/
					//$path = t3lib_extMgm::extPath($extKey)."class.tx_".$clName.".php";
					
					//print "path: ".$path."<br>";
					/*
					if(file_exists($path)) {
						require_once($path);
						
						$class_name = 'tx_'.$clName;
						$instance = t3lib_div::makeInstance($clName);
						
						if($instance instanceof tx_mailform_FE_Addon ) {
							$this->addAddon($instance, $extKey, 'FE');
						}
						
						if($instance instanceof tx_mailform_BE_Addon) {
							$this->addAddon($instance, $extKey, 'BE');
						}
						
					} else {
						die("Addon: $ext_key did not find class to include: $path");
					}
					*/
				} else {
					// Dont load the extension, its not loaded
				}
			}
		}
	}
	
	private function addAddon($object, $extKey, $type="FE") {
		$flag = false;
		foreach($this->addonList as $addon) {
			if($addon['key'] == $extKey && $addon['type'] == $type)
				$flag = true;
		}
		if(!$flag) {
			$this->addonList[] = array('object' => $object, 'key' => $extKey, 'type' => $type);
		}
	}
	
	public static function getInstance() {
		if(empty(self::$instance))
			self::$instance = new tx_mailform_extLoader();
		return self::$instance;
	}
	
	
	/**
	 * Execute SendFormular Function in Addons
	 *
	 */
	public function sendFormular($arg=array()) {
		foreach($this->addonList as $addon) {
			if($addon['type'] == "FE") {
				$addon['object']->formularSent($arg);
			}
		}
	}
}

?>