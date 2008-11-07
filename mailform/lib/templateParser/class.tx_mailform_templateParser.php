<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Sebastian Winterhalder <sw@internetgalerie.ch>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
require_once(t3lib_extMgm::extPath('mailform')."lib/templateParser/class.tx_mailform_templateObject.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/templateParser/class.tx_mailform_parseEngine.php");

/**
 * tx_mailform_templateParser
 *
 * @author Sebastian Winterhalder <sw@internetgalerie.ch>
 * 
 */
class  tx_mailform_templateParser {
			
	static private $instance;
	static public $unallowedArrayKey = 'norm';
	
	private $templateObjects = array();
  
	/**
	* Get Instance
	*
	*@param Object   
	*/        
	static public function getInstance() {
		if(!self::$instance) {
			self::$instance = new tx_mailform_templateParser();
		}
		return self::$instance;
	}
  
	/**
	 * Private Constructor
	 */
	private function __construct() {
		$this->readFile();
	}

	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	public function getFieldTemplatePath() {
		$path = $this->getTemplateRoot()."/mailform_fields.tmpl";
		return $path;
	}
	
	/**
	 * Enter description here...
	 *
	 * @return unknown
	 */
	public function getCssPath() {
		return $this->getTemplateRoot()."/mailform.css";
	}
	
	/**
	 * getRelativeMailCSSPath
	 *
	 * @return String
	 */
	public function getRelativeMailCSSPath() {
		return $this->getTemplateRoot('cssmail_path');
	}

	/**
	 * get Template Object
	 *
	 * @param String $formKey
	 * @param String $elementKey
	 * @return Object
	 */
	public function getTemplateObject($formKey="",$elementKey,$specialElement='') {
		if($specialElement == '') {
			$specialElement = tx_mailform_templateParser::$unallowedArrayKey;
		}
			
		// If Field Typ unknown return Default Field
		if($elementKey === "") {
			$elementKey = "DEFAULT";
		}
		
		if($formKey === "") {
			foreach($this->templateObjects as $key => $fType) {
				foreach($fType as $elKey => $element) {
					if($elKey == $elementKey) {
						if(is_array($element)) {
							if(is_object($element[$specialElement]))
								return $element[$specialElement];
							else
								return $element[tx_mailform_templateParser::$unallowedArrayKey];
						} else
						return $element;
					}
				}
			}
		}

		if(empty($this->templateObjects[$elementKey][$formKey][$specialElement])) {
			die("The Template for the Field ".$elementKey."-".$formKey." was not found: ".$this->getTemplateRoot());
		}
		
		return $this->templateObjects[$elementKey][$formKey][$specialElement];
	}
	
	/**
	 * Get a list of keys
	 *
	 * @param unknown_type $formKey
	 * @param unknown_type $elementKey
	 */
	public function getSpecialTemplates($formKey="",$elementKey) {
	// If Field Typ unknown return Default Field
		if($elementKey === "") {
			$elementKey = "DEFAULT";
		}
		if($formKey === "") {
			foreach($this->templateObjects as $key => $fType) {
				foreach($fType as $elKey => $element) {
					if($elKey == $elementKey) {
						return $element;
					}
				}
			}
		}
		return $this->templateObjects[$elementKey][$formKey];
	}
	
	/**
	 * get Sub Template Object
	 *
	 * @param String $formKey
	 * @param String $elementKey
	 */
	public function getSubTemplateObject($formKey="", $elementKey) {
		
	}

	/**
	 * addTemplateObject
	 *
	 * @param Object $obj
	 */
	public function addTemplateObject($obj) {
		assert(!empty($obj));
	
		$c1 = $c2 = false;
	
		foreach($this->templateObjects as $key => $val) {
			if($obj->getFormType() == $key)
				$c2 = true;
			foreach($val as $key => $formObj) {
				if($obj->getElementType() == $formObj)
					$c1 = true;
			}
		}
		
		if($c1 && $c2) {
			die("Template contains two or more keys that are not unique: ".$obj->getFormType()." [".$obj->getElementType()."]");
		}

		if($arr = $this->isSpecialTemplate($obj->getFormType())) {
			$origSubKey = $arr[0];
			$str = str_replace("[", "", $origSubKey);
			$subKey = str_replace("]", "", $str);

			$formType = str_replace($origSubKey, "", $key);
			$obj->setFormType($formType);
		} else {
			$formType = $obj->getFormType();
			$subKey = tx_mailform_templateParser::$unallowedArrayKey;
		}
		$this->templateObjects[$obj->getElementType()][$formType][$subKey] = $obj;
	}
  
	public function getTemplateRoot($key='standard_template') {
		$res = $this->getTemplateRoot_BE($key);
		return $res;
	}
	
	/**
	 * get Template Root
	 *
	 * @param String $key
	 * @return String
	 */
	public function getTemplateRoot_BE($key='standard_template') {
		global $cObj, $plugin_configuration;

		if(empty($plugin_configuration) && TYPO3_MODE == "BE") {
			$BE_Handler = tx_mailform_BE_Handler::getInstance();
			$plugin_configuration = $BE_Handler->getTSConf();
		}
		
		require_once(t3lib_extMgm::extPath('mailform')."lib/database/class.tx_mailform_db_ttContentRow.php");
		if(empty($cObj))
			$flex = tx_mailform_db_ttContentRow::getInstance()->getFlexformArray();
		else
			$flex = tx_mailform_db_ttContentRow::getInstance($cObj->data['uid'])->getFlexformArray();

		// Check if standard root dir is set
		$standard_template_root = t3lib_extMgm::extPath('mailform')."/template";
		if(!file_exists($standard_template_root)) {
			die("Standard Template Root is not set at ".$standard_template_root);
		}
			
		// Check if standard template is set
		$standard_template = $standard_template_root."/mailform_fields.tmpl";
		if(!file_exists($standard_template))
			die("Standard Template File not found in ".$standard_template_root);

		try {
			$ts_const = tx_mailform_funcLib::parseExtPath($plugin_configuration[$key]);
		} catch (Extension $e) {
			$ts_const = "";
		}
		try {
			$flex_const = tx_mailform_funcLib::parseExtPath($flex['sDEF'][$key]);
		} catch (Extension $e) {
			$flex_const = "";
		}
		
		if(($ts_const == $standard_template || $ts_const == "") 
			&& ($flex_const == $standard_template || $flex_const == "")) {
				// Standard Template
				if(isset($flex['sDEF']['allowStandardTemplate']) && $flex['sDEF']['allowStandardTemplate'] != 1) {
					$sendOperator = tx_mailform_sendOperator::getInstance();
					$sendOperator->addError('Standard Template has been taken.');
				}
      		return $standard_template;
			} else {
	      	// Die TS Konstante wurde abgeändert (normalfall)
	      	if($flex_const != $standard_template && $flex_const != "") {
	      		// TS Konstante wird durch Flexform ueberschrieben
	      		if(file_exists($flex_const)) {
	      			return $flex_const;
	      		}
		      	elseif($this->checkFileExistsForBE($flex_const)) {
					return $this->getBEFileroot($flex_const);
				}
	      		else {
	      			$sendOperator = tx_mailform_sendOperator::getInstance();
	      			$sendOperator->addError('<br><br>Template Error:<br>Flexform Constant is not Properly Set: '.$flex_const.' could not be found. Please type right the Plugin-Flexform<br>If you see this error and you are not administrator please contact your administrator and report this error<br>');
	      		}
	      	}
			
			// Da entweder die flex_form definition nicht gefunden wurde, oder auf standard definiert wurde
			// TS Konstante bzw Standard Template zurückgeben
			if(file_exists($ts_const)) {
				$sendOperator = tx_mailform_sendOperator::getInstance();
				if(!is_file($ts_const))
					$sendOperator->addError("<b>Error</b>: Filename required in TS-Constant editor. Given: ".$ts_const);
				return $ts_const;
			} elseif($this->checkFileExistsForBE($ts_const)) {
				return $this->getBEFileroot($ts_const);
			}
			else {
				if(isset($flex['sDEF']['allowStandardTemplate']) && $flex['sDEF']['allowStandardTemplate'] != 1) {
					$sendOperator = tx_mailform_sendOperator::getInstance();
					$sendOperator->addError('<br>Template Error: <br>Standard Template has been taken. This is not allowed by Flexform Configuration: You can either allow the standard template or correct the wrong path of the template.<br>Given TS-Constant: '.$ts_const);
				}
				return $standard_template;
			}
		}
	}
	
	/**
	 * Check if a file exists when in BE
	 *
	 * @param String $filename
	 */
	private function checkFileExistsForBE($filename) {
		$root = $this->getBEFileroot($filename);
		return (TYPO3_MODE == "BE" && file_exists($root));
	}
	
	private function getBEFileroot($filename) {
		return "../../../../".$filename;
	}
	
	/**
	 * read File ()
	 *
	 */
	private function readFile() {
		$filename = $this->getTemplateRoot();
		$handle = fopen($filename, 'r');

		$c = 0;
		$currentField = false; // Flags
		$subTemplate = false; // Flags
		while (!feof($handle)) {
			$buffer = fgets($handle, 4096);
			$key = $this->parseKey($buffer);

			if($this->isStart($key) && ($this->isLayout($key) || $this->isSpecialTemplate($key) || $this->isForm($key) || $this->isNavi($key)))
			{
				$currentField = $this->getFormType($key); // Set the current form type
				
				$tmpObj = t3lib_div::makeInstance("tx_mailform_templateObject");
				$tmpObj->setFormType($this->getFormType($key));
				$tmpObj->setElementType($this->getElementType($key));
				$tmpObj->setSpecialType($this->getSpecialType($key));

				$this->addTemplateObject($tmpObj);
			}
			else
			{
				
				if($this->isStart($key) && $this->isSubTemplate($key)) {
					// Subtemplate Starting Delimiter
					$subTemplate = true;
					$subObj = t3lib_div::makeInstance("tx_mailform_templateObject");
					$subObj->setFormType($this->getFormType($key));
					$subObj->setElementType($this->getElementType($key));

					$this->getTemplateObject($tmpObj->getFormType(), $tmpObj->getElementType(), $tmpObj->getSpecialType())->setSubElement($subObj);
				} elseif($this->isEnd($key) && $this->isSubTemplate($key)) {
					
					// Subtemplate Ending Delimiter
					$subTemplate = false;
					$tmpObj->appendHtml("###OUTPUT_".strtoupper($subObj->getFormType())."###");
				} elseif($subTemplate) {
					
					// Body of Subtemplate
					$subObj->appendHtml($buffer);
				} else {
					// No Subtemplate
					// Only add Strings to the Template Object, if its not a part of a subtemplate
					if(!empty($buffer) && !empty($tmpObj) && !$this->isEnd($key))
						$tmpObj->appendHtml($buffer);
				}
				if($this->isEnd($key) && ($this->isLayout($key) || $this->isForm($key)))
					$currentField = false;
			}
		}
		fclose ($handle);
	}
	
	/**
	 * parse Key
	 *
	 * @param String $line
	 * @return Mixed
	 */
	private function parseKey($line) {
		// Parse Standard Keys
		if(preg_match('(###[A-Za-z0-9_]{1,}###)', $line, $treffer)) {
			$treffer = str_replace("###", "", $treffer[0]);
			return $treffer;
		}
		// Parse sub keys of a type for special templates
		if(preg_match('(###[A-Za-z0-9_]{1,}\[[A-Za-z0-9_-]{1,}\]###)', $line, $treffer)) {
			$treffer = str_replace("###", "", $treffer[0]);
			return $treffer;
		}
		
		return false;
	}
  
	/**
	 * isDelimiter
	 *
	 * @param String $key
	 * @return Boolean
	 */
	private function isDelimiter($key) {
		return ($this->isLayout($key) 
			||	$this->isSpecialTemplate($key)
			||	$this->isForm($key)
			|| $this->isSubTemplate($key)
			|| $this->isNavi($key)
		);
	}
	
	/**
	 * isStart ($key)
	 *
	 * @param String $key
	 * @return Boolean
	 */
	private function isStart($key) {		
		$res = ( $this->isDelimiter($key)
			&& !$this->isOutput($key)
			&& !(bool)preg_match("(END_)", $key));

		return $res;
	}
	
	/**
	 * isEnd ($key)
	 *
	 * @param String $key
	 * @return Boolean
	 */
	private function isEnd($key) {
		return ( $this->isDelimiter($key)
			&& !$this->isOutput($key)
			&& (bool)preg_match("(END_)", $key));
	}
	
	/**
	 * Checks if the given key is a layout key
	 *
	 * @param String $key
	 * @return Boolean
	 */
	private function isLayout($key) {
		if(preg_match('(LAYOUT_)', $key))
			return true;
		else
			return false;
	}
  	
	/**
	 * Checks if the given key is a Form Key
	 *
	 * @param String $key
	 * @return Boolean
	 */
	private function isForm($key) {
		if(preg_match('(FORM_)', $key))
			return true;
		else
			return false;
	}
	
	/**
	 * Checks if the given key is a Navi Key
	 *
	 * @param String $key
	 * @return Boolean
	 */
	private function isNavi($key) {
		if(preg_match('(NAVI_)', $key))
			return true;
		else
			return false;
	}

	private function isSpecialTemplate($key) {
		if(preg_match('(\[[A-Za-z0-1_-]{1,}\])', $key, $treffer)) {
			return $treffer;
		}
		else
			return false;
	}
	
	/**
  	 * isSubTemplate $key
  	 *
  	 * @param String $key
  	 * @return Boolean
  	 */
	private function isSubTemplate($key) {
		if(preg_match('(SUBEL_)', $key)) {
			return true;
		} else
			return false;
	}

	/**
	 * is Output ($key)
	 *
	 * @param String $key
	 * @return Boolean
	 */
	private function isOutput($key) {
		if(preg_match('(OUTPUT_)', $key))
			return true;
		else
			return false;
	}

	/**
	 * get Form Type with Key
	 *
	 * @param String $key
	 * @return String
	 */
	private function getFormType($key) {
		if($this->isLayout($key))
			$result = str_replace('LAYOUT_', '', $key);
		if($this->isForm($key))
			$result = str_replace('FORM_', '', $key);
		if($this->isNavi($key))
			$result = str_replace('NAVI_', '', $key);
		if($this->isSubTemplate($key))
			$result = str_replace('SUBEL_', '', $key);
		if($this->isOutput($key))
			$result = str_replace('OUTPUT_', '', $key);
		return $result;
	}
	
	/**
	 * getElement Type
	 *
	 * @param String $key
	 * @return String
	 */
	private function getElementType($key) {
		if($this->isLayout($key))
			return 'layout';
		if($this->isForm($key))
			return 'form';
		if($this->isNavi($key))
			return 'navi';
		if($this->isSubTemplate($key))
			return 'subel';
		if($this->isOutput($key))
			return 'output';
		return 'x';
	}
	
	private function getSpecialType($key) {
		if($this->isSpecialTemplate($key)) {
			preg_match('(\[[A-Za-z0-1_-]{1,}\])', $key, $match);
			$str = str_replace("[", "", $match[0]);
			$str = str_replace("]", "", $str);
			return $str;
		} else {
			return false;
		}
	}

}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/templateParser/class.tx_mailform_templateParser.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/templateParser/class.tx_mailform_templateParser.php']);
}
?>