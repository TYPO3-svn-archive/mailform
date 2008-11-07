<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,'editorcfg','tt_content.CSS_editor.ch.tx_mailform_pi1 = < plugin.tx_mailform_pi1.CSS_editor');

t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_mailform_pi1.php','_pi1','list_type',0);

t3lib_extMgm::addTypoScript($_EXTKEY,'setup','tt_content.shortcut.20.0.conf.fe_users = < plugin.tx_mailform_pi1');
?>