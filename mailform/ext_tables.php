<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
$tempColumns = Array (
	"tx_mailform_config" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:mailform/locallang_db.xml:tt_content.tx_mailform_config",		
		"config" => Array (
			"type" => "text",
			"cols" => "48",	
			"rows" => "15",	
			"wizards" => Array (
				"_PADDING" => 2,
				"example" => Array (
					"title" => "Example Wizard:",
					"type" => "script",
					"notNewRecords" => 1,
					"icon" => t3lib_extMgm::extRelPath("mailform")."tt_content_tx_mailform_config/wizard_icon.gif",
					"script" => t3lib_extMgm::extRelPath("mailform")."tt_content_tx_mailform_config/index.php",
				),
			),
		)
	),
);

t3lib_div::loadTCA("tt_content");
t3lib_extMgm::addTCAcolumns("tt_content",$tempColumns,1);

t3lib_extMgm::addPlugin(array('LLL:EXT:mailform/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');

t3lib_extMgm::addStaticFile($_EXTKEY,"pi1/static/","mailform");

$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages,recursive';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform,tx_mailform_config;;;;1-1-1';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY.'/flexform_ds.xml');

if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_mailform_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_mailform_pi1_wizicon.php';
?>