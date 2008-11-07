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
require_once(t3lib_extMgm::extPath('mailform')."lib/templateParser/class.tx_mailform_templateParser.php");
require_once(t3lib_extMgm::extPath('mailform')."lib/templateParser/class.tx_mailform_parseEngine.php");

/**
* mailform module tx_mailform_emailGenerator
*
* @author       Sebastian Winterhalder <sw@internetgalerie.ch>
* 
*/
class tx_mailform_emailGenerator
{
	
	private $subject;
	private $sender;
	private $receiver;
	private $htmlMail = false;
	private $content = "";
	private $fields;
	private $attachments = array();
	private $tableFields;
	private $header = "";
	private $footer = "";
	private $hideData = false; // If this value is true, the form data will be sent
	
	/**
	 * Constructor
	 *
	 */
	public function __construct($cObj) {
		global $FE_Handler,$plugin_configuration;
		// Create empty Array List of fields
		$this->fields = array();
		$this->cObj = $cObj;
		
		$this->receiver['emails'] = array();
		$this->tableFields = $FE_Handler->getTableFields();
		$this->fields = $FE_Handler->getFieldElements();
	}
	
	/**
	 * Send the Email
	 *
	 */
	public function send() {
		if($this->htmlMail)
			$this->generateEmail();
		else
			$this->generateRawText();
	}
	
	/**
	 * Generiere und sende das HTML Email
	 *
	 */
	private function generateEmail() {
		require_once(t3lib_extMgm::extPath('mailform').'lib/layout/table/class.tx_mailform_table.php');
		
    	
		$ufids = array();
		
		$table = new tx_mailform_table();
		$table->setCellpadding(0);
		$table->setCellspacing(0);
		$table->setWidth("100%");
		$table->setBorder(false);
		$table->addCssClass('mainTable');
		
		if($this->header != '') {
		
			$parseEngine = new tx_mailform_parseEngine();
			$parseEngine->loadData($this->header);
			
			$row = new tx_mailform_tr();
    		$td = new tx_mailform_td();
    		$td->setColspan(1);
    		$td->addCssClass('pageHeader');
    		$td->setContent($parseEngine->getParsed());
    		$row->addTd($td);
    		$table->addRow($row);
		}
		
		$pageTable = new tx_mailform_table();
		$pageTable->setCellpadding(1);
		$pageTable->setCellspacing(1);
		$pageTable->setComment('Page');
		$pageTable->setWidth("100%");
		$pageTable->setBorder(false);
		
		$pageConfig = tx_mailform_configData::getInstance()->getPageConfig();

    	foreach($this->tableFields as $pKey => $page ) {
	    		if(count($this->tableFields) > 1 || !empty($pageConfig[$pKey]['pagetitle'])) {
	    			// Wenn mehr als 1 Seite existieren, seite angeben
	    			$tr = new tx_mailform_tr();
	    			$td = new tx_mailform_td();
	    			$td->setColspan(1);
	    			$td->addStyle('background-color: #BBB; color: #FFF; font-weight: bold; font-size: 12px;');
	    			
	    			// Set email page title
	    			if(!empty($pageConfig[$pKey]['pagetitle']))
	    				$td->setContent($pageConfig[$pKey]['pagetitle']);
	    			else
	    				$td->setContent('Page: '.($pKey+1));
	    			$tr->addTd($td);
	    			$pageTable->addRow($tr);
	    		}
    		
	    		/** Parse Pages */
				foreach($page as $rKey => $row) {
	
					$tr = new tx_mailform_tr();
					$cCol = 0;
					foreach($row as $cKey => $col) {
						$td = new tx_mailform_td();
						
						$td->setColspan($col->getColspan());
						$td->setRowspan($col->getRowspan());
						$td->setWidth($col->getWidth());
						$td->setHeight($col->getHeight());
						$td->addCssClass('mainTable');
						$td->addCssClass('bottomBorder');
						$td->setValign('top');
						
						$formElements = $col->getFormElements();
						$innerTable = new tx_mailform_table();
						$innerTable->setWidth("100%");
						$innerTable->setCellspacing(0);
						$innerTable->setCellpadding(0);
						
						/**
						 * If the data should not be displayed, hide this loop
						 */
						if(!$this->hideData && $col->getBooleanOfCondition()) {
							foreach($formElements as $form) {
								//$form = $this->getFormWithUid($formKey);
								if($form !== false) {
									$iTr = $form->getForm()->getEmailResult(false);
									if($iTr instanceof tx_mailform_tr) {
										$innerTable->addRow($iTr);
									}
								}
							}
						}
						
						if(count($formElements) > 0)
							$td->setContent($innerTable->getElementRendered());
						if(!$col->isPlaceholder())
						$tr->addTd($td);
						
						$cCol++;
					}
					if($cCol > 0)
						$pageTable->addRow($tr);
				}
    		}
		$row = new tx_mailform_tr();
    	$td = new tx_mailform_td();
    	$td->setColspan(1);
    	$td->setRowspan(1);
    	$td->setValign('top');
    	$td->setContent($pageTable->getElementRendered());
		$row->addTd($td);
		$table->addRow($row);
		
		if($this->footer != '') {
			$row = new tx_mailform_tr();
    		$td = new tx_mailform_td();
    		$td->setColspan(1);
    		$td->addCssClass('pageHeader');
    		$td->setContent($this->footer);
    		$row->addTd($td);
    		$table->addRow($row);
		}
		
		$formCont = $table->getElementRendered();

		$templateParser = tx_mailform_templateParser::getInstance();
		$templateParser->getRelativeMailCSSPath();
		$cssPath = $templateParser->getRelativeMailCSSPath();
		if(file_exists($templateParser->getRelativeMailCSSPath())) {
			$fRes = fopen($cssPath, 'r');
			$string = '';
			while($sApp = fread($fRes, 1024)) {
				$string .= $sApp;
			}
		}
		else
			$string = '
				       body {font-family: verdana, arial;}
				       table {background-color: #FFFFFF; font-size: 11px;}
				       td {border-bottom: 1px #000000 solid;}
				       .pageHeader { background-color: #FFFFFF; font-weight: bold; height: 45px; }
				       .mailContent { font-weight: normal; font-align: left; }
				       .mailLabel { font-weight: bold; width: 200px; }
				       .mainTable { background-color: #FEFEFE; font-size: 11px; border:0px none #FFF;}
				       .bottomBorder { border-bottom: 1px solid #000000; }
			';
		
		#hier m�ssen die entsprechenden Parameter gesetzt werden

		$html_start='<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<html><head>
<style type="text/css">
      '.$string.'
</style>
<title>'.$subject.'</title>
</head>
<body>
		';
		$html_end= '</body></html>';

        $this->content = $html_start.$formCont.$html_end;

		$this->sendEmail();
	}
	
	private function getFormWithUid($uid) {
		foreach($this->fields as $fieldArr) {
			foreach($fieldArr as $form) {
				if($form->getForm()->getUFID() == $uid)
					return $form;
			}
		}
		return false;
	}
	
	/**
	 * Generiere und sende ein Text mail
	 *
	 */
	private function generateRawText() {
		// Initialisiere variablem
		$emailText = empty($this->subject) ? "No Subject\n" : $this->subject."\n";

    	$emailText .= $this->header."\n";

    	$pageCount = 1;
    	foreach($this->fields as $page) {
    		if($pageCount > 1)
				$emailText .= "---------------- Seite ".($pIndex + 1)." ----------------\n";
    		foreach($page as $field) {
    			$emailText .= $field->getForm()->getEmailResult(true);
    		}
    		$pageCount++;
    	}

		$emailText .= "\n".$this->footer."\n";

		$emailText = 'Diese Option \'Rohes Textmail\' ist zur Zeit Deaktiviert. Bitte kontaktieren Sie den Entwickler um das Problem zu beheben.
		Sie k�nnen alternativ aber das HTML Mail brauchen, welches eine differenzierte Darstellung zur Verf�gung stellt.';

		$this->content = $emailText;
		$this->sendEmail();
	}
	
	/**
	 * send email with php mailer
	 *
	 */
	private function sendEmail() {
		require_once(t3lib_extMgm::extPath('mailform')."lib/smtp/class.phpmailer.php");
		$phpMailer = new PHPMailer();
		$phpMailer->CharSet = 'utf-8';

		if($this->SMTP_activated()) {
			$phpMailer->IsSMTP(); // telling the class to use SMTP
			$phpMailer->Host       = $this->SMTP_getServer(); // SMTP server
			$phpMailer->SMTPAuth = true;
			$phpMailer->Username = $this->SMTP_getUser();
			$phpMailer->Password = $this->SMTP_getPassword();
			$phpMailer->From       = $this->SMTP_getFromMail();
			$phpMailer->FromName   = $this->SMTP_getFromName();
			$phpMailer->SMTPSecure = $this->SMTP_getSecure();
			$port = $this->SMTP_getPort();
			if(!empty($port)) {
				$phpMailer->Port	= $this->SMTP_getPort();
			}
		} else {
			$phpMailer->From       = $this->SMTP_getFromMail();
			$phpMailer->FromName   = $this->SMTP_getFromName();
		}
		
		foreach($this->attachments as $attachment) {
			$phpMailer->AddAttachment($attachment);
		}
		
		$phpMailer->IsHTML($this->htmlMail);
		if($this->htmlMail)
			$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
		
		$phpMailer->Subject = $this->subject;
		$phpMailer->Body = $this->content;
		
		foreach($this->receiver['emails'] as $mail) {
			$phpMailer->AddAddress($mail, $mail);
		}

		$phpMailer->send();
	}
	
	/**
	 * Setters and getters
	 */
	
	/**
	 * Set content header
	 *
	 * @param String $header
	 */
	public function setContentHeader($header) {
		$this->header = $header;
	}
	
	/**
	 * Set content Footer
	 *
	 * @param String $footer
	 */
	public function setContentFooter($footer) {
		$this->footer = $footer;
	}
	
	/**
	 * is smtp activated?
	 *
	 * @return Boolean
	 */
	public function SMTP_activated() {
		global $plugin_configuration;
		return ($plugin_configuration['mail_use_smtp'] == "1");
	}
	
	/**
	 * Get User of SMTP
	 *
	 * @return String
	 */
	public function SMTP_getPassword() {
		global $plugin_configuration;
		return $plugin_configuration['mail_smtp_password'];
	}
	
	/**
	 * Get User of SMTP
	 *
	 * @return String
	 */
	public function SMTP_getUser() {
		global $plugin_configuration;
		return $plugin_configuration['mail_smtp_user'];
	}
	
	/**
	 * get SMTP Server
	 *
	 * @return String
	 */
	public function SMTP_getServer() {
		global $plugin_configuration;
		return $plugin_configuration['mail_smtp'];
	}
	
	/**
	 * get SMTP Port
	 *
	 * @return String
	 */
	public function SMTP_getPort() {
		global $plugin_configuration;
		return $plugin_configuration['mail_port'];
	}
	
	/**
	 * get SMTP From email
	 *
	 * @return String
	 */
	public function SMTP_getFromMail() {
		global $plugin_configuration;

		if(!empty($this->sender['email'])) {
			return $this->sender['email'];
		}
		return $plugin_configuration['mail_smtp_from'];
	}
	
	/**
	 * get SMTP From name
	 *
	 * @return String
	 */
	public function SMTP_getFromName() {
		global $plugin_configuration;

		if(!empty($this->sender['name']))
			return $this->sender['name'];

		return $plugin_configuration['mail_smtp_from_name'];
	}
	
	public function SMTP_getSecure() {
		global $plugin_configuration;
		return $plugin_configuration['mail_smtp_secure'];
	}

	/**
	 * Set HTML Mail Flag
	 *
	 * @param Boolean $boolean
	 */
	public function setHtmlMail($boolean) {
		if((gettype($boolean) != "boolean"))
			throw new Exception('Given argument is not boolean, but '.gettype($boolean));
		$this->htmlMail = $boolean;
	}
	
	/**
	 * Set the Email content
	 *
	 * @param unknown_type $content
	 */
	public function setEmailContent($content) {
		$this->content = $content;
	}
	
	public function hideFormData($boolean) {
		if($boolean == 0 || $boolean == 1) {
			$this->hideData = $boolean;
		} else {
			throw new Exception('Wrong input in tx_mailform_emailGenerator::setEmailContent($boolean)');
		}
	}
	
	/**
	 * 
	 *
	 * @param unknown_type $string
	 */
	public function setSubject($string) {
		$this->subject = $string;
	}
	
	public function addAttachment($attachment) {
		$this->attachments[] = $attachment;
	}
	
	/**
	 * Set receivers (array)
	 *
	 * @param Array $array
	 */
	public function addReceivers($array) {
		if(!is_array($array))
			throw new Exception('Array expected in emailGenerator.setReceiver()');
		
		foreach($array as $email) {
			$this->addReceiver($email);
		}
	}
	
	public function addReceiversCSV($csv) {
		if(!gettype($csv) == "string")
			throw new Exception("String expected in emailGenerator.setReceiverCSV()");
		$csv = str_replace(" ", "", $csv);	
		$list = split(",", $csv);
		
		$this->addReceivers($list);
	}
	
	/**
	 * Add a receiver
	 *
	 * @param String $receiver
	 */
	public function addReceiver($receiver) {
		if(strlen($receiver) <= 0)
			throw new Exception('Empty sender given');
		if(!$this->isEmail($receiver))
			throw new Exception('Invalid Email given');
			
			$this->receiver['emails'][] = $receiver;
	}
	
	public function getReceivers() {
		return $this->receiver['emails'];
	}
		
	/**
	 * Delete all receivers
	 *
	 */
	public function clearReceiver() {
		$this->receiver['emails'] = array();
	}
	
	/**
	 * Set a sender
	 * (If sender is strlen > 0 it will be set)
	 * (If Sender strlen = 0: The internal variable will be unset
	 *
	 * @param String $string
	 */
	public function setSender($string) {
		if(strlen($string) > 0)
			$this->sender['email'] = $string;
		else
			unset($this->sender['email']);
	}
	
	/**
	 * Set the Sender name
	 * (If sender name is strlen > 0 it will be set)
	 * (If sender name strlen = 0: the internal variable will be unset
	 *
	 * @param String $string
	 */
	public function setSenderName($string) {
		if(strlen($string) > 0)
			$this->sender['name'] = $string;
		else
			unset($this->sender['name']);
	}
	
	/**
	 * Check if given email is a valid email string
	 *
	 * @param String $string
	 * @return Boolean
	 */
	public function isEmail($string) {
		return t3lib_div::validEmail($string);
	}
	
	/**
	 * CSV From Array
	 *
	 * @param unknown_type $array
	 * @return unknown
	 */
	private function getCSVFromArray($array) {
		if(!is_array($array))
			$array = array();
		return implode(",", $array);
	}
	
	/**
	 * Set ArrayList of fields
	 *
	 * @param unknown_type $array
	 */
	public function DEPRECATED_setFields($array, $tableFields) {
		if(!is_array($array))
			throw new Exception("ArrayList expected. ".ucfirst(gettype($array))." given!");
		$this->fields = $array;
		if(!is_array($tableFields))
			throw new Exception("ArrayList expected. ".ucfirst(gettype($array))." given!");
		$this->tableFields = $tableFields;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/pi1/mail/class.tx_mailform_emailGenerator.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/mailform/pi1/mail/class.tx_mailform_emailGenerator.php']);
}
?>