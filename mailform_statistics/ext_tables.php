<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (TYPO3_MODE == 'BE')	{
		
	t3lib_extMgm::addModule('tools','txmailformstatisticsM1','',t3lib_extMgm::extPath($_EXTKEY).'mod1/');
}

$TCA["tx_mailformstatistics_settings"] = array (
	"ctrl" => array (
		'title' => 'LLL:EXT:mailform_statistics/locallang_db.xml:tx_mailformstatistics_settings',		
		'label' => 'uid',	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => "ORDER BY crdate",	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_mailformstatistics_settings.gif',
	),
	"feInterface" => array (
		"fe_admin_fieldList" => "hidden",
	)
);
?>