<?php

########################################################################
# Extension Manager/Repository config file for ext: "mailform_statistics"
#
# Auto generated 07-11-2008 18:24
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Mailform Statistik Modul',
	'description' => 'This extension allows to save and display Emails. Provides Excel and CVS download.',
	'category' => 'misc',
	'shy' => 0,
	'version' => '1.0.5',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'mod1',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
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
			'mailform' => '0.8.5-2.0.0',
			'phpexcel_library' => '1.0.0-2.0.0',
			'php' => '5.1.2-5.9.9',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:40:{s:9:"ChangeLog";s:4:"4cf3";s:10:"README.txt";s:4:"ee2d";s:31:"class.tx_mailformstatistics.php";s:4:"057c";s:12:"ext_icon.gif";s:4:"4d7b";s:14:"ext_tables.php";s:4:"0a64";s:14:"ext_tables.sql";s:4:"d255";s:28:"ext_typoscript_constants.txt";s:4:"82a3";s:24:"ext_typoscript_setup.txt";s:4:"ea65";s:39:"icon_tx_mailformstatistics_settings.gif";s:4:"475a";s:13:"locallang.xml";s:4:"5aa3";s:16:"locallang_db.xml";s:4:"409f";s:7:"tca.php";s:4:"18d7";s:12:"wiz_icon.png";s:4:"fafa";s:14:"doc/manual.sxw";s:4:"8f8b";s:19:"doc/wizard_form.dat";s:4:"5a0e";s:43:"lib/class.tx_mailformstatistics_display.php";s:4:"9d00";s:23:"mod1/OLE_PPS_File0BBpu1";s:4:"120e";s:23:"mod1/OLE_PPS_File9V8m60";s:4:"22b2";s:23:"mod1/OLE_PPS_FileGQlbyj";s:4:"a0a1";s:23:"mod1/OLE_PPS_FilebiGVjE";s:4:"a0a1";s:23:"mod1/OLE_PPS_FilefzqFLm";s:4:"5e80";s:23:"mod1/OLE_PPS_FilehVhBmn";s:4:"22b2";s:23:"mod1/OLE_PPS_FilerC4VFH";s:4:"22b2";s:14:"mod1/clear.gif";s:4:"cc11";s:13:"mod1/conf.php";s:4:"61db";s:14:"mod1/index.php";s:4:"7b9b";s:18:"mod1/locallang.xml";s:4:"9e72";s:22:"mod1/locallang_mod.xml";s:4:"1f4a";s:28:"mod1/mailform_statistics.png";s:4:"8063";s:13:"mod1/mod1.css";s:4:"beb0";s:19:"mod1/moduleicon.gif";s:4:"4d7b";s:49:"mod1/display/class.tx_mailformstatistics_disp.php";s:4:"514e";s:55:"mod1/display/class.tx_mailformstatistics_mailDetail.php";s:4:"a4d1";s:24:"mod1/gfx/diagramCook.gif";s:4:"4d7b";s:18:"mod1/gfx/email.png";s:4:"af58";s:25:"mod1/gfx/email_delete.png";s:4:"a265";s:23:"mod1/gfx/email_edit.png";s:4:"ec84";s:23:"mod1/gfx/page_excel.png";s:4:"9aeb";s:17:"mod1/gfx/zoom.png";s:4:"b362";s:23:"mod1/gfx/verlauf/v1.gif";s:4:"7ed9";}',
);

?>