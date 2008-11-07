<?php
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

/**
* mailform module
*
* @author       Sebastian Winterhalder <sw@internetgalerie.ch>
* 
*/
class tx_mailform_fileHandler
{
	private $TEMP_DIR;
	private static $SELF_INSTANCE;
	private $files = array();
	private $errors = array();
	
	private $default_EXPIRATION = 3600; // 60 Minutes
	
	/**
	 * Constructor
	 *
	 */
	public function __construct() {
		$this->TEMP_DIR = t3lib_extMgm::extPath('mailform')."temp/";
		if(!file_exists($this->TEMP_DIR)) {
			if(!mkdir($this->TEMP_DIR))
				$this->errors[] = "Could not create directory in: ".$this->TEMP_DIR;
		}
		
			
		$this->loadTempFile();
	}
	
	/**
	 * getInstance()
	 *
	 * @return Self Instance
	 */
	public static function getInstance() {
		if(empty(tx_mailform_fileHandler::$SELF_INSTANCE)) {
			tx_mailform_fileHandler::$SELF_INSTANCE = new tx_mailform_fileHandler();
		}
		return tx_mailform_fileHandler::$SELF_INSTANCE;
	}
	
	/**
	* Read the file in chunks
	* So php does not abort   
	*
	*
	*/           
	private function readfile_chunked($filename,$retbytes=true) {
		$chunksize = 1*(1024*1024); // how many bytes per chunk
		$buffer = '';
		$cnt =0;
		
		$handle = fopen($filename, 'rb');
		if ($handle === false) {
			return false;
		}
		
		
		while (!feof($handle)) {
			$buffer = fread($handle, $chunksize);
			echo $buffer;
			
			if ($retbytes) {
				$cnt += strlen($buffer);
			}
		}
		
		$status = fclose($handle);
		if ($retbytes && $status) {
			return $cnt; // return num. bytes delivered like readfile() does.
		}
		return $status;
	}
  
	/**
	 * Send a file to the users browser
	 *
	 *@param $filename String
	 *@param $rename="" String   
	 */           
	function sendFileToUser($filename, $rename = "") {
		$extension = split(".", $z->data_name);
		$extension = $extension[sizeof($extension)];
	
	
		if(!file_exists($filename)) {
			throw new Exception("File could not be found: ".$filename);
		}
		
		$f = fopen($filename, "rb");
		$content_len = filesize($filename);
		$content_file = fread($f, $content_len);
		fclose($f);
		
		@ob_end_clean();
		@ini_set('zlib.output_compression', 'Off');
		
		@header('Pragma: public');
		
		switch( $extension ) {
			case "pdf": $ctype="application/pdf";
				break;
			case "exe": $ctype="application/octet-stream";
				break;
			case "zip": $ctype="application/zip";
				break;
			case "psd": $ctype="application/psd";
				break;
			case "pmd": $ctype="application/pmd";
				break;
			case "doc": $ctype="application/msword";
				break;
			case "xls": $ctype="application/vnd.ms-excel";
				break;
			case "xlsx": $ctype="application/vnd.ms-excel";
				break;
			case "ppt": $ctype="application/vnd.ms-powerpoint";
				break;
			case "gif": $ctype="image/gif";
				break;
			case "png": $ctype="image/png";
				break;
			case "jpg": $ctype="image/jpg";
				break;
			default:    $ctype="application/force-download";
				break;
		}
		
		@header("Expires: 0");
		@header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: $ctype; Charset=utf-8");
		@header("Content-Disposition: attachment; filename=".($rename!="" ? $rename : $filename).";");
		@header("Content-Transfer-Encoding: binary");
		@header("Content-Length: ".$content_len);	 
		
		echo $this->readfile_chunked($filename);
		@unlink($filename);
	}
  
	/**
	 * Get an unique filename and delete all old files from the temp
	 *
	 *@return String   
	*/
	public static function getUniqueFilename($file, $root) {
		if(!file_exists($root))
			throw new Exception('File or Dir not Found: '.$root);
		$fileRelease = 30; // Seconds
		$files = scandir($root);
		
		// Delete Temp
		foreach($files as $rootFiles) {
		$fName = split("_", $rootFiles);
			if($fName[0] != "." && $fName[0] != ".." && $fName[0] != "index.html" && intval($fName[0])+$fileRelease < time()) {
				@unlink($root.$rootFiles);
			}
		}
		
		return $root.time()."_".$file;
	}
 
  
	/**
	 * Delete Old Files
	 *
	 */
	public function resetFiles($fieldUID) {
		$GLOBALS['TYPO3_DB']->exec_DELETEquery("tx_mailform_fileHandler", 'field_id=\''.$fieldUID.'\' AND user_sess_id = \''.session_id()."'");
	}
	
	/**
	 * Creates and reads the Template File
	 *
	 */
	private function loadTempFile() {
		$allowedFiles = array();
		$dbRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', "tx_mailform_fileHandler", "1=1");
		
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($dbRes) ) {
			if(!$row['file_expired']) {
				$checkData = $this->getRowInformation($row['filecode']);
				$checkData['uid'] = $row['uid'];
				if($checkData['expire'] < time()) {
					@unlink($this->TEMP_DIR.$checkData['md5name']);
					$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_mailform_fileHandler', 'uid='.$row['uid'], array('file_expired' => 1, 'db_expire' => (time()+$this->default_EXPIRATION)));
				} else {
					$allowedFiles[] = $checkData;
				}
			} else {
				if($row['db_expire'] < time() && $row['db_expire'] > 0)
					$GLOBALS['TYPO3_DB']->exec_DELETEquery("tx_mailform_fileHandler", 'uid='.$row['uid']);
			}
		}

		
		$filesInDir = scandir($this->TEMP_DIR);
		foreach($filesInDir as $file) {
			if($file != ".." && $file != ".") {
				$flag = false;
				foreach($allowedFiles as $allowedFile) {
					if($allowedFile['md5name'] == $file) {
						$flag = true;
						
					}
				}
				
				if(!$flag) {
					@unlink($this->TEMP_DIR.$file);
				}
			}
		}
		
		$this->files = $allowedFiles;
	}
	
	/**
	 * Move an uploaded file, stored in Database. Can be get again trough this Handler
	 *
	 * @param String $fileroot
	 * @param String $filename
	 * @param String DB Identifier
	 * @param Array $additionalData
	 */
	public function moveUploadedFile($fileroot, $filename, $dbIdentifier, $additionalData) {
		$md5names = array();
		foreach($this->files as $file) {
			$md5names[] = $file['md5name'];
		}
		do {
			$NewName = md5($filename.rand(100,999));
		} while(array_search($NewName, $md5names) !== false);

		if(!move_uploaded_file($fileroot, $this->TEMP_DIR.$NewName))
			throw new Exception("Uploaded file cannot be moved");
		
		$filecode = array_merge($additionalData, array("md5name" => $NewName, 'crtime' => time(), 'expire' => (time()+$this->default_EXPIRATION)));
		$filecode = $this->setRowInformation($filecode);
		
		$dbInput = array('db_expire' => (time()+$this->default_EXPIRATION), 'filecode' => $filecode, 'file_expired' => 0, 'user_sess_id' => session_id(), 'field_id' => $dbIdentifier);
		$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_mailform_fileHandler', $dbInput);
		$fileId = mysql_insert_id();
		return $fileId;
	}
	
	public function getTempDir() {
		assert(!empty($this->TEMP_DIR));
		return $this->TEMP_DIR;
	}
	
	/**
	 * Enter description here...
	 *
	 */
	public function getFileroot($fileid) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery("*", "tx_mailform_fileHandler", "uid=".$fileid);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
	
		return array_merge($this->getRowInformation($row['filecode']), array('temp_root' => $this->TEMP_DIR, 'fileid'=>$row['uid']));
	}
	
	
	/**
	 * Get a Group of Files with the DB Identifier
	 *
	 * @param Varchar $dbIdentifier
	 */
	public function getFiles($dbIdentifier) {
		$sql = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_mailform_fileHandler', 'field_id=\''.$dbIdentifier.'\' AND user_sess_id=\''.session_id().'\' AND NOT file_expired = 1');
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($sql)) {
			$r[] = array_merge($this->getRowInformation($row['filecode']), array('temp_root' => $this->TEMP_DIR, 'uid' => $row['uid']));
		}
		return $r;
	}
	
	public function deleteFile($fileid) {
		$info = $this->getFileroot($fileid);
		@unlink($this->TEMP_DIR.$info['md5name']);
		$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_mailform_fileHandler', 'uid='.$fileid);
		foreach($this->files as $key => $file) {
			if($file['uid'] == $fileid) {
				unset($this->files[$key]);
			}
		}
	}
	
	public function renameFileWithUID($uid, $normToMd5=false) {
		foreach($this->files as $file) {
			if($file['uid'] == $uid) {
				$set = $file;
				break;	
			}
		}
		
		if(empty($set)) {
			return false;
		}
		
		if($normToMd5)
			@rename($this->TEMP_DIR.$file['name'], $this->TEMP_DIR.$file['md5name']);
		else
			@rename($this->TEMP_DIR.$file['md5name'], $this->TEMP_DIR.$file['name']);
	}
	
	
	public function getFilePath($uid, $md5code=false) {
		foreach($this->files as $file) {
			if($file['uid'] == $uid) {
				$set = $file;
				break;	
			}
		}
		
		if(empty($set))
			return false;
		
		if($md5code)
			return $this->TEMP_DIR.$file['md5name'];
		else
			return $this->TEMP_DIR.$file['name'];
	}
	
	/**
	 * get Array of String
	 *
	 * @param String $string
	 * @return Array
	 */
	private function getRowInformation($string) {
		$res = array();
		$elements = split(";", $string);
		foreach($elements as $element) {
			$part = split("\|",$element);
			if($part[0] != "")
				$res[$part[0]] = $part[1];
		}
		return $res;
	}
	
	/**
	 * Get String from Array
	 *
	 * @param Array $array
	 * @return String
	 */
	private function setRowInformation($array) {
		$res = "";
		foreach($array as $key => $element) {
			$res .= "$key|$element;";	
		}
		return $res;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/class.tx_mailform_fileHandler.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/lib/class.tx_mailform_fileHandler.php']);
}

?>