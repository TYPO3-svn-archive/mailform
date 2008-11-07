<?
session_start();
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
require_once(t3lib_extMgm::extPath("mailform")."/formTypesModel/class.tx_mailform_formAbstract.php");
require_once(t3lib_extMgm::extPath("mailform")."/lib/class.tx_mailform_fileHandler.php");

/**
* mailform module tt_content_tx_mailform_forms
*
* @author       Sebastian Winterhalder <sw@internetgalerie.ch>
*/
class tx_mailform_formFile extends tx_mailform_formAbstract {

	protected $input_field_cols = 30;
	// Harmful file extensions are in $autoDenyFile, The User cannot change this
	protected $autoDenyFile = array("exe", "php", "js", "asp", "cgi", "reg","chm","cnf","hta","ins","jse","job","lnk","pif","scf","sct","shb","shs","es","vb","xnk","cer","its","mau","md","prf","pst","tmp","vsmacros","vs","ws");
	// Explicit allowed File types, the user can change it for every field, this is a standard, not fully complete
	protected $explicitAllow = array("zip", "tar", "gz", "txt", "text", "z", "rar", "pdf", "psd", "tiff", "gif", "jpeg", "jpg", "raw", "png", "gif", "bmp", "ppm", "pgm", "pbm", "pnm", "xlsx", "docx", "accdb", "xls", "doc", "xsn", "xml", "avi", "mov", "wav", "wma", "mpeg", "mpg", "mpeg2", "mpeg3", "mpeg4", "mp2", "mp3", "mp4", "m3u", "m4u", "css", "csv", "czip", "dat", "dcx", "egg", "eps", "faq", "hex", "java", "js", "lib", "midi", "mid", "qt", "ps", "pfa", "pfb", "pict", "pic", "pct");
	protected $explicitDeny = array();
	protected $standardMaxFilesize = 4096; // kB
	protected $standardMinFilesize = 0; // kB
	protected $be_typeImage = '../gfx/type/file.gif';
	protected $file;
	protected $tmp_path;
	protected $tmp_time = 120; // seconds
	
	protected $files = array();
	/**
	 * Field Initialization
	 *
	 */
  	protected function fieldInit() {
    	$this->hasInitialized = true;
    	$this->files = array();
    	$this->tmp_path = t3lib_extMgm::extPath("mailform")."temp/";
    	$FileHandler = tx_mailform_fileHandler::getInstance();
    	
    	$P = t3lib_div::_GP('delFile');
  		if(!empty($P)) {
  			$FileHandler->deleteFile($P);	
  		}
  		
  		if(!empty($_FILES)) {
			$post = array('name' => $this->getUploadedFileInfo('name'),
					'type' => $this->getUploadedFileInfo('type'),
					'tmp_name' => $this->getUploadedFileInfo('tmp_name'),
					'error' => $this->getUploadedFileInfo('error'),
					'size' => $this->getUploadedFileInfo('size'));
			try {
			$fileId = $FileHandler->moveUploadedFile($post['tmp_name'], $post['name'], $this->getUFID(), $post);

			if(empty($this->configData['forms_file_allowmultiple'])) {
				
				$files = $FileHandler->getFiles($this->getUFID());
					foreach($files as $file) {
						if($file['uid'] != $fileId)
						$FileHandler->deleteFile($file['uid']);
					}
			}
			
			
			} catch (Exception $e) {
				// DEBUG !
			}

			$post['fileid'] = $fileId;
			$this->postData[] = $post;
		}

		$FH = tx_mailform_fileHandler::getInstance();
  		$this->files = $FH->getFiles($this->getUFID());
	}
	
	public function resetForm() {
		$FH = tx_mailform_fileHandler::getInstance();
		$FH->resetFiles($this->getUFID());
	}
	
	/**
	 * Setup FE Post
	 * Overwrite formAbstract::setupFEPost
	 */
	public function DEPRECATED_setupFEPost() {
		if(!empty($_FILES)) {
			$post = array('name' => $this->getUploadedFileInfo('name'),
					'type' => $this->getUploadedFileInfo('type'),
					'tmp_name' => $this->getUploadedFileInfo('tmp_name'),
					'error' => $this->getUploadedFileInfo('error'),
					'size' => $this->getUploadedFileInfo('size'));
			$this->setPostData($post);

			if(file_exists($this->postData['tmp_name'])) {
				$FileHandler->moveUploadedFile($this->postData['tmp_name'], $this->postData['name'], $this->getUFID(),$additionalData);
			}
		}
	}
	
	/**
	 * Returns The Rendered Template
	 *
	 * @return unknown
	 */
  	protected function renderFrontend() {
  		global $FE_Handler;
  		
  		if(!is_array($this->files))
  			$this->files = array();
		$output = "<table>";
		$UH = new tx_mailform_urlHandler();
  		foreach($this->files as $data) {
  			if(!empty($this->configData['forms_file_allowmultiple']))
  			$delLink = "(<a href=\"".$UH->getCurrentUrl(array('delFile'))."&delFile=".$data['uid']."\">delete</a>)";
  			$output .= "<tr><td>File: ".$data['name']." ".$delLink." </td></tr>";
  		}
  		$output .= "</table>";
  		
  		$fileField = $output;
		$fileField .= '
		<input id="" type="hidden" name="'.$this->getUniqueFieldname().'[file_submit]" value="1" />
		<input id="'.$this->getUniqueIDName("input").'" class="tx_mailform_file" name="'.$this->getUniqueFieldname().'" type="file" size="'.$this->configData['input_field_cols'].'" />';
		
		$this->templateObject->addOutput("FORMVALUE", $this->configData['forms_reload_button']);
		$this->templateObject->addOutput("FORMNAME", tx_mailform_naviAbstract::getVarPrefix().'[direct][0]');
		$hiddenField = '<input type="hidden" value="'.($FE_Handler->getCurrentPage()).'" name="'.tx_mailform_naviAbstract::getVarPrefix().'[direct][1]">';
		$this->templateObject->addOutput("FORMHIDDEN", $hiddenField);
		
		return $this->getWithTemplate($this->configData['label'], $fileField, $this->isFormRequired(), -1);
  	}
  	
	/**
	 * Enter description here...
	 *
	 * @return Array
	 */
	protected function renderHtml() {
		global $LANG;
		$array = array();
		$array[] = $this->makeTitleRow($LANG->getLL('form_options'));
		$array[] = $this->makeRow($LANG->getLL('form_input_cols'), $this->makeInputField('input_field_cols', $this->input_field_cols));
		
		$bool = empty($this->configData['forms_file_allowmultiple']) ? false:true;
		$array[] = $this->makeRow($LANG->getLL('forms_file_allowmultiple'), $this->makeCheckbox('forms_file_allowmultiple', $bool));
		
		$bool = empty($this->configData['forms_file_checkfile']) ? false:true;
		$array[] = $this->makeRow($LANG->getLL('forms_file_checkfile'), $this->makeCheckbox('forms_file_checkfile', $bool));
		if(empty($this->configData['forms_reload_button']))
			$this->configData['forms_reload_button'] = $LANG->getLL('forms_reload_button_value');
		
		$array[] = $this->makeRow($LANG->getLL('forms_reload_button'), $this->makeInputField('forms_reload_button', $this->configData['forms_reload_button']));

		if(empty($this->configData['forms_file_checkfile'])) {
			$hidden = $this->makeHidden('validation_type', 'file');
			
			if(!isset($this->configData['forms_file_minfilesize']))
			$this->configData['forms_file_minfilesize'] = $this->standardMinFilesize;
			$array[] = $this->makeRow($LANG->getLL('forms_file_minfilesize'), $this->makeInputField('forms_file_minfilesize', $this->configData['forms_file_minfilesize']).$hidden." kB");
			
			if(!isset($this->configData['forms_file_maxfilesize']))
				$this->configData['forms_file_maxfilesize'] = $this->standardMaxFilesize;
			$array[] = $this->makeRow($LANG->getLL('forms_file_maxfilesize'), $this->makeInputField('forms_file_maxfilesize', $this->configData['forms_file_maxfilesize'])." kB");
		
			$array[] = $this->makeRow($LANG->getLL('forms_file_denied_mediatype'), str_replace(',', ', ', implode(',', $this->autoDenyFile)));
	
			$array[] = $this->makeRow($LANG->getLL('forms_file_allow_explicit_cb'), $this->makeCheckbox('forms_file_allow_explicit_cb', ($this->configData['forms_file_allow_explicit_cb'] == on)));

			if($this->configData['forms_file_allow_explicit_cb'] == "on") {
				if(empty($this->configData['forms_file_allow_explicit_ext'])) $this->configData['forms_file_allow_explicit_ext'] = implode(", ", $this->explicitAllow);
				$array[] = $this->makeRow($LANG->getLL('forms_file_allow_explicit_ext'), $this->makeTextarea('forms_file_allow_explicit_ext', $this->configData['forms_file_allow_explicit_ext'], 30, 8), $LANG->getLL('forms_file_csvdef'));
			}
			else {
				if(empty($this->configData['forms_file_denied_explicit_ext']))
					$this->configData['forms_file_denied_explicit_ext'] = implode(", ", $this->explicitDeny);
				$array[] = $this->makeRow($LANG->getLL('forms_file_denied_explicit_ext'), $this->makeTextarea('forms_file_denied_explicit_ext', $this->configData['forms_file_denied_explicit_ext'], 30, 8), $LANG->getLL('forms_file_csvdef'));
			}
			$array[] = $this->makeTitleRow($LANG->getLL('error_handling'));
			
			
			if(empty($this->forms_file_dsize_error))
				$this->configData['forms_file_dsize_error'] = $LANG->getLL('forms_file_size_error');
			if(empty($this->forms_file_dext_error))
				$this->configData['forms_file_dext_error'] = $LANG->getLL('forms_file_ext_error');	
			$array[] = $this->makeRow($LANG->getLL('forms_file_dsize_error'), $this->makeInputField('forms_file_dsize_error', $this->configData['forms_file_dsize_error']));
			$array[] = $this->makeRow($LANG->getLL('forms_file_dext_error'), $this->makeInputField('forms_file_dext_error', $this->configData['forms_file_dext_error']));
		}
		
		return $array;
	}
	
	/**
	 * check if it contains any attachment
	 *
	 * @return Boolean
	 */
	public function containsAttachment() {
		if(sizeof($this->files) > 0 && $this->isFormValid()) {
			return true;
		}
		else return false;
	}

	/**
	 * getFilename()
	 *
	 * @return String
	 */
	public function getFilename() {
		return $this->files;
		/**
		if($this->containsAttachment())
			return $this->postData['name'];
		*/
	}
	
	/**
	 * Extracts the file extension
	 * Result from $filename = 'example.txt' => 'txt'
	 * 
	 * @param String $filename
	 * @return String
	 */
	private function  get_extension($filename)
	{
		$file_extension = split("\.",$filename);

		return $file_extension[sizeof($file_extension) - 1];
	}
	
	/**
	 * Formats bytes
	 *
	 * @param Int $bytes
	 * @return String
	 */
	public function byte_calculator($bytes)
	{
		if($bytes < 1024)
		{
			$result = $bytes;
			$sign   = "B";
		}
		elseif($bytes >= 1024 && $bytes < 1024^2)
		{
			$result = ($bytes/1024);
			$sign   = "kB";	
		}
		else
		{
			$result = ($bytes/(1024^2));
			$sign   = "mB";
		}
		return number_format($result, 0,".","'")." ".$sign;
	}
	
      
	/**
	 * Inherit from tx_mailform_formAbstract
	 *
	 * Returns true if
	 * 	- The field is not Required
	 * 	- The field has a field value strlen > 0
	 * Returns false if
	 * 	- The field value has a strlen <= 0
	 * 
	 * @param String $fieldValue
	 * @return Boolean
	 */
	public function enteredRequired() {
		if($this->isFormRequired()) {
			if(count($this->files) > 0) {
				foreach($this->files as $file) {
					if($file['name'] != "")
					return true;
				}
			}
			return false;
		}
		else return true;
	}

	/**
	 * removeWhitespaces($str)
	 *
	 * @param String $str
	 * @return String
	 */
  	private function removeWhitespaces($str) {
  		$str = str_replace(" ", "", $str);
  		$str = str_replace("\n ", "", $str);
  		return $str;
  	}
	
	/**
	 * Inherit from tx_mailform_formAbstract
	 *
	 * Returns true if
	 * 	- The field has no validation
	 * 	- The field contains valid data
	 * Returns false if
	 * 	- The field has a validation and contains invalid data
	 * 
	 * Validation of data
	 * 
	 * @param unknown_type $fieldValue
	 * @return unknown
	 */
	public function validateField() {
		if(!$this->alreadyValidated) {
			// Make sure the field is not twice validated
			parent::validateField();
			
			if(sizeof($this->files) > 0 ) {
				foreach($this->files as $filekey => $file) {
					$fileExtension = $this->get_extension($file['name']);
					
					$PRdeniedExt = !empty($this->configData['forms_file_denied_explicit_ext']) ? split(",", $this->removeWhitespaces($this->configData['forms_file_denied_explicit_ext'])) : array();
					$deniedExtensions = array_merge($this->autoDenyFile, $PRdeniedExt);
					$allowedExtensions = (!empty($this->configData['forms_file_allow_explicit_ext'])) ? split(",", $this->removeWhitespaces($this->configData['forms_file_allow_explicit_ext'])) : array();
					
					// All Checks are depending from $_FILES
					// Check if the file extension is valid
					if($this->configData['forms_file_allow_explicit_cb'] == "on") {
						if($file['name'] != '') {
							if(array_search($fileExtension, $this->autoDenyFile) === false && $fileExtension != "") {
								if(array_search($fileExtension, $allowedExtensions) !== false && $fileExtension != "") {
									$this->appendFormDataValid(1, '');
								} else {
									$this->appendFormDataValid(0, $this->configData['forms_file_dext_error']." (".$file['name'].")");
								}
							} else {
								$this->appendFormDataValid(0, $this->configData['forms_file_dext_error']." (".$file['name'].")");
							}
						} else {
							$this->appendFormDataValid(0, "FILENAME EMPTY / hould not APPEAR");
						}
					} else {
						if($file['name'] != '') {
							// File must not be explicitly allowed, but must pass harmful extensions or disallowed extensions
							if(array_search($fileExtension, $deniedExtensions) === false && $fileExtension != "") {
								// Do nothing the file is allowed
								$this->appendFormDataValid(1, '');
							} else
								$this->appendFormDataValid(0, $this->configData['forms_file_dext_error']." (".$file['name'].")");		
						} else {
							$this->appendFormDataValid(1, '');
						}
					}
				}
			}
			else $this->appendFormDataValid(1, '');
		}
	}

	/**
	 *
	 */
	public function getFieldValue() {
		return $this->getUploadedFileInfo('name');
	}
	
	/**
	 * Parent overwrite of Email Value
	 *
	 * @param unknown_type $rawText
	 */
	public function getEmailValue($rawText=false) {
		$res = array();
		if(empty($this->files) || !is_array($this->files))
			$this->files = array();
			
		foreach($this->files as $file) {
			$res[] = $file['name'];
		}
		return implode(",", $res);
	}

  
	// Inherit from tx_mailform_formAbstract
	/**
	 * @deprecated 
	 *
	 * @return unknown
	 */
  	public function validFieldPost() {
  		//$this->validateField();
	   return $this->isFormValid();
	}

	/**
	 * getUploadedFileInfo($key)
	 *
	 * @param String $key
	 * Keys are allowed:
	 * size, name, tmp_name, type, error
	 * @return Mixed
	 */
	public function getUploadedFileInfo($key) {
		$res = $_FILES[$this->formPrefix][$key][tx_mailform_FE_Handler::getContentUID()][$this->getUFID()][$this->configData['type']];
		return $res;
	}
	
	/**
	 * savePost($argument)
	 * not yet implemented
	 *
	 * @param mixed $arg
	 */
	public function savePost($mailid) {
		$rawString = t3lib_div::array2xml($this->files);

		return $this->dbHan_saveField($mailid, '', '', $rawString, '');
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_formFile.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/formTypesModel/models/class.tx_mailform_formFile.php']);
}
?>