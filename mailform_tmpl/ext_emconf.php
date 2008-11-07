<?php

########################################################################
# Extension Manager/Repository config file for ext: "mailform_tmpl"
#
# Auto generated 07-11-2008 18:25
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Mailform Templates',
	'description' => 'Allows to manage Templates in the mailform wizard',
	'category' => 'misc',
	'shy' => 0,
	'version' => '1.0.4',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => 'fileadmin/ext/mailform/mailform_tmpl/',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Sebastian Winterhalder',
	'author_email' => 'sw@internetgalerie.ch',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'mailform' => '0.8.3-0.9.10',
			'php' => '5.1.2-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:27:{s:9:"ChangeLog";s:4:"07e8";s:10:"README.txt";s:4:"ee2d";s:25:"class.tx_mailformtmpl.php";s:4:"7a20";s:12:"ext_icon.gif";s:4:"e673";s:17:"ext_localconf.php";s:4:"b0d2";s:14:"ext_tables.php";s:4:"d41d";s:14:"ext_tables.sql";s:4:"20ed";s:28:"ext_typoscript_constants.txt";s:4:"a98f";s:24:"ext_typoscript_setup.txt";s:4:"f69c";s:13:"locallang.xml";s:4:"0d45";s:12:"wiz_icon.png";s:4:"53da";s:14:"doc/manual.sxw";s:4:"5ec9";s:19:"doc/wizard_form.dat";s:4:"3096";s:20:"doc/wizard_form.html";s:4:"e721";s:22:"gfx/arrow_right_up.gif";s:4:"9ec6";s:12:"gfx/lupe.png";s:4:"e51d";s:21:"gfx/save_template.png";s:4:"1e9f";s:16:"gfx/settings.png";s:4:"a24a";s:37:"lib/class.tx_mailformtmpl_display.php";s:4:"3510";s:37:"lib/class.tx_mailformtmpl_history.php";s:4:"e84e";s:40:"lib/class.tx_mailformtmpl_historyObj.php";s:4:"fa15";s:36:"lib/class.tx_mailformtmpl_loader.php";s:4:"0ef7";s:38:"lib/class.tx_mailformtmpl_settings.php";s:4:"b106";s:41:"lib/class.tx_mailformtmpl_templateObj.php";s:4:"2613";s:52:"xml_templates/150408_Contact Bu..._Internetga....xml";s:4:"d5b0";s:52:"xml_templates/150408_Leer (Kein..._Internetga....xml";s:4:"38de";s:52:"xml_templates/160408_Contact Fo..._Internetga....xml";s:4:"4fa0";}',
);

?>