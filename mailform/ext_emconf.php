<?php

########################################################################
# Extension Manager/Repository config file for ext: "mailform"
#
# Auto generated 07-11-2008 17:58
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Mailform',
	'description' => 'Simple wizard interface it supports a multi col system, Database storage, very good choice of field elements, an add-on platform for third developer, a very usable form wizard and a lot more. Documentation on: http://mailform.typo3-extensions.ch. Check out other Mailform Addons.',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '0.9.13',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'tt_content_tx_mailform_config',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => 'tt_content',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Sebastian Winterhalder',
	'author_email' => 'typo3@internetgalerie.ch',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:360:{s:9:"ChangeLog";s:4:"ac1e";s:10:"README.txt";s:4:"9fa9";s:14:"deprecated.txt";s:4:"8a12";s:12:"ext_icon.gif";s:4:"e673";s:17:"ext_localconf.php";s:4:"e258";s:15:"ext_php_api.dat";s:4:"f765";s:14:"ext_tables.php";s:4:"20ed";s:14:"ext_tables.sql";s:4:"6ff5";s:28:"ext_typoscript_constants.txt";s:4:"e7fe";s:24:"ext_typoscript_setup.txt";s:4:"a585";s:15:"flexform_ds.xml";s:4:"1300";s:13:"locallang.xml";s:4:"ebc6";s:16:"locallang_db.xml";s:4:"c851";s:23:"backup/backup_tmpl.tmpl";s:4:"8917";s:14:"doc/manual.sxw";s:4:"eca1";s:65:"formTypesModel/class.DEPRECATED_tx_mailform_formStaticInclude.php";s:4:"9e17";s:45:"formTypesModel/class.tx_mailform_Validate.php";s:4:"c6b6";s:51:"formTypesModel/class.tx_mailform_emailRecipient.php";s:4:"0b36";s:41:"formTypesModel/class.tx_mailform_form.php";s:4:"c023";s:49:"formTypesModel/class.tx_mailform_formAbstract.php";s:4:"53e6";s:49:"formTypesModel/class.tx_mailform_formMultiple.php";s:4:"4dc5";s:56:"formTypesModel/class.tx_mailform_formOutputGenerator.php";s:4:"5a4b";s:48:"formTypesModel/class.tx_mailform_formRequest.php";s:4:"a1c8";s:51:"formTypesModel/class.tx_mailform_formValidation.php";s:4:"e554";s:51:"formTypesModel/class.tx_mailform_layoutAbstract.php";s:4:"b5b4";s:49:"formTypesModel/class.tx_mailform_naviAbstract.php";s:4:"fde6";s:55:"formTypesModel/class.tx_mailform_naviSubmitAbstract.php";s:4:"0f24";s:50:"formTypesModel/class.tx_mailform_optionElement.php";s:4:"c7bc";s:48:"formTypesModel/class.tx_mailform_postHandler.php";s:4:"0b83";s:53:"formTypesModel/class.tx_mailform_singletonCaptcha.php";s:4:"c316";s:49:"formTypesModel/class.tx_mailform_xajaxHandler.php";s:4:"f59a";s:65:"formTypesModel/formAbstract/class.tx_mailform_formAbstract_BE.php";s:4:"d663";s:65:"formTypesModel/formAbstract/class.tx_mailform_formAbstract_FE.php";s:4:"3d63";s:68:"formTypesModel/formAbstract/class.tx_mailform_formAbstract_State.php";s:4:"10bb";s:55:"formTypesModel/models/class.tx_mailform_formCaptcha.php";s:4:"9d8f";s:56:"formTypesModel/models/class.tx_mailform_formCheckbox.php";s:4:"1e2d";s:59:"formTypesModel/models/class.tx_mailform_formContelement.php";s:4:"2164";s:55:"formTypesModel/models/class.tx_mailform_formDefault.php";s:4:"4bc2";s:52:"formTypesModel/models/class.tx_mailform_formFile.php";s:4:"10e3";s:54:"formTypesModel/models/class.tx_mailform_formHidden.php";s:4:"8215";s:56:"formTypesModel/models/class.tx_mailform_formPassword.php";s:4:"662f";s:53:"formTypesModel/models/class.tx_mailform_formRadio.php";s:4:"aedb";s:54:"formTypesModel/models/class.tx_mailform_formSelect.php";s:4:"5157";s:61:"formTypesModel/models/class.tx_mailform_formStaticcountry.php";s:4:"f259";s:52:"formTypesModel/models/class.tx_mailform_formText.php";s:4:"bd85";s:56:"formTypesModel/models/class.tx_mailform_formTextarea.php";s:4:"25b1";s:57:"formTypesModel/models/class.tx_mailform_formTextwdesc.php";s:4:"17b5";s:55:"formTypesModel/models/class.tx_mailform_layoutError.php";s:4:"94e5";s:61:"formTypesModel/models/class.tx_mailform_layoutHtmlelement.php";s:4:"eaa2";s:59:"formTypesModel/models/class.tx_mailform_layoutSeparator.php";s:4:"5f9f";s:55:"formTypesModel/models/class.tx_mailform_layoutTitle.php";s:4:"322d";s:56:"formTypesModel/models/class.tx_mailform_naviNextpage.php";s:4:"4ace";s:56:"formTypesModel/models/class.tx_mailform_naviPagenavi.php";s:4:"2eac";s:60:"formTypesModel/models/class.tx_mailform_naviPreviouspage.php";s:4:"c869";s:53:"formTypesModel/models/class.tx_mailform_naviReset.php";s:4:"6a5b";s:54:"formTypesModel/models/class.tx_mailform_naviSubmit.php";s:4:"550e";s:62:"formTypesModel/models/class.tx_mailform_naviSubmitextended.php";s:4:"a55a";s:59:"formTypesModel/models/class.tx_mailform_naviSubmitimage.php";s:4:"dcef";s:18:"gfx/arrow_left.gif";s:4:"1050";s:25:"gfx/arrow_left_denied.gif";s:4:"a881";s:22:"gfx/arrow_left_new.gif";s:4:"2d06";s:19:"gfx/arrow_right.gif";s:4:"b37b";s:26:"gfx/arrow_right_denied.gif";s:4:"744b";s:23:"gfx/arrow_right_new.gif";s:4:"eb99";s:19:"gfx/button_down.gif";s:4:"fa54";s:20:"gfx/button_empty.gif";s:4:"4efb";s:17:"gfx/button_up.gif";s:4:"0cc7";s:16:"gfx/database.gif";s:4:"9274";s:19:"gfx/delete_cell.gif";s:4:"2b41";s:18:"gfx/display_be.gif";s:4:"552a";s:20:"gfx/display_befe.gif";s:4:"60aa";s:21:"gfx/display_email.gif";s:4:"05c4";s:18:"gfx/display_fe.gif";s:4:"e94b";s:23:"gfx/display_feemail.gif";s:4:"80fd";s:18:"gfx/display_no.gif";s:4:"cf53";s:13:"gfx/edit2.gif";s:4:"3248";s:20:"gfx/extended_wiz.gif";s:4:"a5c6";s:15:"gfx/garbage.gif";s:4:"90c6";s:12:"gfx/help.gif";s:4:"9b26";s:12:"gfx/help.png";s:4:"034d";s:23:"gfx/icon_fatalerror.gif";s:4:"6dcc";s:15:"gfx/icon_ok.gif";s:4:"d103";s:21:"gfx/icon_required.gif";s:4:"29f1";s:19:"gfx/insert_cell.gif";s:4:"8cdc";s:18:"gfx/insert_col.gif";s:4:"c9ac";s:22:"gfx/insert_col_top.gif";s:4:"6520";s:18:"gfx/insert_row.gif";s:4:"cfea";s:23:"gfx/insert_row_left.gif";s:4:"6daa";s:18:"gfx/merge_cell.gif";s:4:"2dfe";s:23:"gfx/merge_cell_down.gif";s:4:"26b5";s:23:"gfx/multi_documents.png";s:4:"5509";s:14:"gfx/new_el.gif";s:4:"591c";s:20:"gfx/no_reference.gif";s:4:"0d10";s:20:"gfx/no_reference.png";s:4:"e3c6";s:20:"gfx/reference_ok.gif";s:4:"8216";s:25:"gfx/reference_warning.gif";s:4:"020f";s:18:"gfx/remove_col.gif";s:4:"4075";s:22:"gfx/remove_col_top.gif";s:4:"d6da";s:18:"gfx/remove_row.gif";s:4:"0b94";s:23:"gfx/remove_row_left.gif";s:4:"27ae";s:18:"gfx/split_cell.gif";s:4:"b680";s:23:"gfx/split_cell_down.gif";s:4:"eb81";s:20:"gfx/standard_wiz.gif";s:4:"1af6";s:12:"gfx/undo.gif";s:4:"f707";s:20:"gfx/type/captcha.gif";s:4:"63ff";s:21:"gfx/type/checkbox.gif";s:4:"5116";s:18:"gfx/type/email.gif";s:4:"4459";s:18:"gfx/type/error.gif";s:4:"7769";s:18:"gfx/type/field.gif";s:4:"68ff";s:17:"gfx/type/file.gif";s:4:"c451";s:17:"gfx/type/html.gif";s:4:"bd44";s:18:"gfx/type/radio.gif";s:4:"5122";s:19:"gfx/type/select.gif";s:4:"8c1e";s:22:"gfx/type/separator.gif";s:4:"3050";s:21:"gfx/type/standard.gif";s:4:"c119";s:19:"gfx/type/submit.gif";s:4:"aa9d";s:21:"gfx/type/textarea.gif";s:4:"1472";s:38:"hooks/class.tx_mailform_BE_Handler.php";s:4:"3e8b";s:38:"hooks/class.tx_mailform_FE_Handler.php";s:4:"fd09";s:35:"hooks/class.tx_mailform_Handler.php";s:4:"6b7b";s:39:"lib/class.tx_mailform_WizardWrapper.php";s:4:"a3a1";s:50:"lib/class.tx_mailform_cfgArrayString_Converter.php";s:4:"2b1c";s:36:"lib/class.tx_mailform_checkInput.php";s:4:"00f7";s:38:"lib/class.tx_mailform_excelHandler.php";s:4:"0f4d";s:40:"lib/class.tx_mailform_fieldGenerator.php";s:4:"fc07";s:45:"lib/class.tx_mailform_fieldValueContainer.php";s:4:"e106";s:37:"lib/class.tx_mailform_fileHandler.php";s:4:"132c";s:33:"lib/class.tx_mailform_funcLib.php";s:4:"bdba";s:37:"lib/class.tx_mailform_performance.php";s:4:"6f77";s:38:"lib/class.tx_mailform_processInput.php";s:4:"f919";s:36:"lib/class.tx_mailform_urlHandler.php";s:4:"8eb6";s:50:"lib/addonInterface/class.tx_mailform_extLoader.php";s:4:"ca44";s:53:"lib/addonInterface/interface.tx_mailform_BE_Addon.php";s:4:"1838";s:53:"lib/addonInterface/interface.tx_mailform_FE_Addon.php";s:4:"3273";s:47:"lib/controller/class.tx_mailform_observable.php";s:4:"620b";s:45:"lib/controller/class.tx_mailform_observer.php";s:4:"ada7";s:45:"lib/database/class.tx_mailform_dbInstance.php";s:4:"b4a4";s:54:"lib/database/class.tx_mailform_db_mailFieldContent.php";s:4:"00e2";s:49:"lib/database/class.tx_mailform_db_mailsOfForm.php";s:4:"7bbe";s:50:"lib/database/class.tx_mailform_db_ttContentRow.php";s:4:"4d61";s:50:"lib/datastructures/class.tx_mailform_ArrayList.php";s:4:"5ded";s:49:"lib/divers/class.tx_mailform_contentContainer.php";s:4:"d09f";s:55:"lib/layout/attributes/class.tx_mailform_attr_accept.php";s:4:"ff36";s:62:"lib/layout/attributes/class.tx_mailform_attr_acceptcharset.php";s:4:"07ca";s:55:"lib/layout/attributes/class.tx_mailform_attr_action.php";s:4:"e4b2";s:54:"lib/layout/attributes/class.tx_mailform_attr_align.php";s:4:"7585";s:52:"lib/layout/attributes/class.tx_mailform_attr_alt.php";s:4:"65c0";s:55:"lib/layout/attributes/class.tx_mailform_attr_border.php";s:4:"338e";s:60:"lib/layout/attributes/class.tx_mailform_attr_cellpadding.php";s:4:"da3e";s:60:"lib/layout/attributes/class.tx_mailform_attr_cellspacing.php";s:4:"9321";s:56:"lib/layout/attributes/class.tx_mailform_attr_checked.php";s:4:"fb0f";s:53:"lib/layout/attributes/class.tx_mailform_attr_cols.php";s:4:"48ed";s:56:"lib/layout/attributes/class.tx_mailform_attr_colspan.php";s:4:"9d30";s:57:"lib/layout/attributes/class.tx_mailform_attr_cssclass.php";s:4:"845f";s:52:"lib/layout/attributes/class.tx_mailform_attr_dir.php";s:4:"6250";s:57:"lib/layout/attributes/class.tx_mailform_attr_disabled.php";s:4:"cd4f";s:56:"lib/layout/attributes/class.tx_mailform_attr_enctype.php";s:4:"3d51";s:55:"lib/layout/attributes/class.tx_mailform_attr_height.php";s:4:"c47a";s:51:"lib/layout/attributes/class.tx_mailform_attr_id.php";s:4:"a642";s:54:"lib/layout/attributes/class.tx_mailform_attr_label.php";s:4:"5362";s:53:"lib/layout/attributes/class.tx_mailform_attr_lang.php";s:4:"4688";s:55:"lib/layout/attributes/class.tx_mailform_attr_method.php";s:4:"6951";s:57:"lib/layout/attributes/class.tx_mailform_attr_multiple.php";s:4:"7f32";s:53:"lib/layout/attributes/class.tx_mailform_attr_name.php";s:4:"ab15";s:55:"lib/layout/attributes/class.tx_mailform_attr_onblur.php";s:4:"29f6";s:57:"lib/layout/attributes/class.tx_mailform_attr_onchange.php";s:4:"88da";s:56:"lib/layout/attributes/class.tx_mailform_attr_onfocus.php";s:4:"1685";s:56:"lib/layout/attributes/class.tx_mailform_attr_onreset.php";s:4:"a145";s:57:"lib/layout/attributes/class.tx_mailform_attr_onselect.php";s:4:"81c4";s:57:"lib/layout/attributes/class.tx_mailform_attr_onsubmit.php";s:4:"deef";s:53:"lib/layout/attributes/class.tx_mailform_attr_rows.php";s:4:"f38b";s:56:"lib/layout/attributes/class.tx_mailform_attr_rowspan.php";s:4:"a4bc";s:57:"lib/layout/attributes/class.tx_mailform_attr_selected.php";s:4:"461b";s:53:"lib/layout/attributes/class.tx_mailform_attr_size.php";s:4:"bd9f";s:52:"lib/layout/attributes/class.tx_mailform_attr_src.php";s:4:"e515";s:54:"lib/layout/attributes/class.tx_mailform_attr_style.php";s:4:"5bbc";s:56:"lib/layout/attributes/class.tx_mailform_attr_summary.php";s:4:"4ccd";s:59:"lib/layout/attributes/class.tx_mailform_attr_tableindex.php";s:4:"096d";s:55:"lib/layout/attributes/class.tx_mailform_attr_target.php";s:4:"85af";s:54:"lib/layout/attributes/class.tx_mailform_attr_title.php";s:4:"cd38";s:53:"lib/layout/attributes/class.tx_mailform_attr_type.php";s:4:"b219";s:55:"lib/layout/attributes/class.tx_mailform_attr_valign.php";s:4:"664c";s:54:"lib/layout/attributes/class.tx_mailform_attr_value.php";s:4:"8a3c";s:54:"lib/layout/attributes/class.tx_mailform_attr_width.php";s:4:"5fc1";s:53:"lib/layout/attributes/class.tx_mailform_attribute.php";s:4:"10ee";s:60:"lib/layout/attributes/class.tx_mailform_attributeFactory.php";s:4:"c44c";s:42:"lib/layout/css/class.tx_mailform_style.php";s:4:"f7cf";s:40:"lib/layout/div/class.tx_mailform_div.php";s:4:"2f34";s:46:"lib/layout/form/class.tx_mailform_checkbox.php";s:4:"8b01";s:46:"lib/layout/form/class.tx_mailform_htmlform.php";s:4:"4d01";s:43:"lib/layout/form/class.tx_mailform_input.php";s:4:"b193";s:44:"lib/layout/form/class.tx_mailform_option.php";s:4:"a344";s:44:"lib/layout/form/class.tx_mailform_select.php";s:4:"20ac";s:46:"lib/layout/form/class.tx_mailform_textarea.php";s:4:"796a";s:40:"lib/layout/img/class.tx_mailform_img.php";s:4:"e00b";s:49:"lib/layout/interface/class.tx_mailform_parent.php";s:4:"9928";s:56:"lib/layout/interface/interface.tx_mailform_I_content.php";s:4:"00b9";s:55:"lib/layout/interface/interface.tx_mailform_I_layout.php";s:4:"eab3";s:64:"lib/layout/interface/interface.tx_mailform_I_multipleContent.php";s:4:"814c";s:59:"lib/layout/interface/interface.tx_mailform_Iattr_accept.php";s:4:"691a";s:66:"lib/layout/interface/interface.tx_mailform_Iattr_acceptcharset.php";s:4:"1a06";s:59:"lib/layout/interface/interface.tx_mailform_Iattr_action.php";s:4:"23ac";s:58:"lib/layout/interface/interface.tx_mailform_Iattr_align.php";s:4:"58fd";s:56:"lib/layout/interface/interface.tx_mailform_Iattr_alt.php";s:4:"f180";s:59:"lib/layout/interface/interface.tx_mailform_Iattr_border.php";s:4:"631a";s:64:"lib/layout/interface/interface.tx_mailform_Iattr_cellpadding.php";s:4:"2302";s:64:"lib/layout/interface/interface.tx_mailform_Iattr_cellspacing.php";s:4:"440b";s:60:"lib/layout/interface/interface.tx_mailform_Iattr_checked.php";s:4:"d7b8";s:57:"lib/layout/interface/interface.tx_mailform_Iattr_cols.php";s:4:"414e";s:60:"lib/layout/interface/interface.tx_mailform_Iattr_colspan.php";s:4:"6349";s:61:"lib/layout/interface/interface.tx_mailform_Iattr_cssclass.php";s:4:"e2b2";s:56:"lib/layout/interface/interface.tx_mailform_Iattr_dir.php";s:4:"67bb";s:61:"lib/layout/interface/interface.tx_mailform_Iattr_disabled.php";s:4:"68c5";s:60:"lib/layout/interface/interface.tx_mailform_Iattr_enctype.php";s:4:"204e";s:59:"lib/layout/interface/interface.tx_mailform_Iattr_height.php";s:4:"7b39";s:55:"lib/layout/interface/interface.tx_mailform_Iattr_id.php";s:4:"1a93";s:58:"lib/layout/interface/interface.tx_mailform_Iattr_label.php";s:4:"8ecb";s:57:"lib/layout/interface/interface.tx_mailform_Iattr_lang.php";s:4:"5523";s:59:"lib/layout/interface/interface.tx_mailform_Iattr_method.php";s:4:"166e";s:61:"lib/layout/interface/interface.tx_mailform_Iattr_multiple.php";s:4:"202a";s:57:"lib/layout/interface/interface.tx_mailform_Iattr_name.php";s:4:"be5c";s:59:"lib/layout/interface/interface.tx_mailform_Iattr_onblur.php";s:4:"d71c";s:61:"lib/layout/interface/interface.tx_mailform_Iattr_onchange.php";s:4:"9788";s:60:"lib/layout/interface/interface.tx_mailform_Iattr_onfocus.php";s:4:"d447";s:60:"lib/layout/interface/interface.tx_mailform_Iattr_onreset.php";s:4:"8568";s:61:"lib/layout/interface/interface.tx_mailform_Iattr_onselect.php";s:4:"955a";s:61:"lib/layout/interface/interface.tx_mailform_Iattr_onsubmit.php";s:4:"ede5";s:57:"lib/layout/interface/interface.tx_mailform_Iattr_rows.php";s:4:"5c68";s:60:"lib/layout/interface/interface.tx_mailform_Iattr_rowspan.php";s:4:"563b";s:61:"lib/layout/interface/interface.tx_mailform_Iattr_selected.php";s:4:"3c67";s:57:"lib/layout/interface/interface.tx_mailform_Iattr_size.php";s:4:"bea4";s:56:"lib/layout/interface/interface.tx_mailform_Iattr_src.php";s:4:"f182";s:58:"lib/layout/interface/interface.tx_mailform_Iattr_style.php";s:4:"f415";s:60:"lib/layout/interface/interface.tx_mailform_Iattr_summary.php";s:4:"80d5";s:63:"lib/layout/interface/interface.tx_mailform_Iattr_tableindex.php";s:4:"3b20";s:59:"lib/layout/interface/interface.tx_mailform_Iattr_target.php";s:4:"447b";s:58:"lib/layout/interface/interface.tx_mailform_Iattr_title.php";s:4:"8110";s:57:"lib/layout/interface/interface.tx_mailform_Iattr_type.php";s:4:"9f48";s:59:"lib/layout/interface/interface.tx_mailform_Iattr_valign.php";s:4:"2807";s:58:"lib/layout/interface/interface.tx_mailform_Iattr_value.php";s:4:"e7ae";s:58:"lib/layout/interface/interface.tx_mailform_Iattr_width.php";s:4:"863d";s:44:"lib/layout/table/class.tx_mailform_table.php";s:4:"12c5";s:41:"lib/layout/table/class.tx_mailform_td.php";s:4:"4c60";s:41:"lib/layout/table/class.tx_mailform_tr.php";s:4:"ecf9";s:61:"lib/pluginInterface/interface.tx_mailform_pluginInterface.php";s:4:"d41d";s:42:"lib/post/class.tx_mailform_postHandler.php";s:4:"ddb3";s:22:"lib/smtp/ChangeLog.txt";s:4:"eef4";s:16:"lib/smtp/LICENSE";s:4:"ef93";s:15:"lib/smtp/README";s:4:"0abe";s:28:"lib/smtp/class.phpmailer.php";s:4:"bac9";s:23:"lib/smtp/class.pop3.php";s:4:"77eb";s:23:"lib/smtp/class.smtp.php";s:4:"67a7";s:26:"lib/smtp/codeworxtech.html";s:4:"9a98";s:31:"lib/smtp/examples/contents.html";s:4:"b4c2";s:28:"lib/smtp/examples/index.html";s:4:"01a0";s:43:"lib/smtp/examples/pop3_before_smtp_test.php";s:4:"bb5f";s:32:"lib/smtp/examples/test_gmail.php";s:4:"89c9";s:31:"lib/smtp/examples/test_mail.php";s:4:"d999";s:35:"lib/smtp/examples/test_sendmail.php";s:4:"86cd";s:31:"lib/smtp/examples/test_smtp.php";s:4:"03fd";s:35:"lib/smtp/examples/images/bkgrnd.gif";s:4:"a290";s:38:"lib/smtp/examples/images/phpmailer.gif";s:4:"f8f0";s:38:"lib/smtp/examples/images/phpmailer.png";s:4:"cf1d";s:43:"lib/smtp/examples/images/phpmailer_mini.gif";s:4:"2937";s:39:"lib/smtp/language/phpmailer.lang-br.php";s:4:"6746";s:39:"lib/smtp/language/phpmailer.lang-ca.php";s:4:"259d";s:39:"lib/smtp/language/phpmailer.lang-cz.php";s:4:"cb55";s:39:"lib/smtp/language/phpmailer.lang-de.php";s:4:"d09d";s:39:"lib/smtp/language/phpmailer.lang-dk.php";s:4:"45cc";s:39:"lib/smtp/language/phpmailer.lang-en.php";s:4:"4b76";s:39:"lib/smtp/language/phpmailer.lang-es.php";s:4:"e892";s:39:"lib/smtp/language/phpmailer.lang-et.php";s:4:"26ef";s:39:"lib/smtp/language/phpmailer.lang-fi.php";s:4:"85c9";s:39:"lib/smtp/language/phpmailer.lang-fo.php";s:4:"5b89";s:39:"lib/smtp/language/phpmailer.lang-fr.php";s:4:"601f";s:39:"lib/smtp/language/phpmailer.lang-hu.php";s:4:"ae15";s:39:"lib/smtp/language/phpmailer.lang-it.php";s:4:"b28c";s:39:"lib/smtp/language/phpmailer.lang-ja.php";s:4:"c89c";s:39:"lib/smtp/language/phpmailer.lang-nl.php";s:4:"96d3";s:39:"lib/smtp/language/phpmailer.lang-no.php";s:4:"0694";s:39:"lib/smtp/language/phpmailer.lang-pl.php";s:4:"204e";s:39:"lib/smtp/language/phpmailer.lang-ro.php";s:4:"48d6";s:39:"lib/smtp/language/phpmailer.lang-ru.php";s:4:"5a31";s:39:"lib/smtp/language/phpmailer.lang-se.php";s:4:"d38f";s:39:"lib/smtp/language/phpmailer.lang-tr.php";s:4:"9a49";s:32:"lib/smtp/test/phpmailer_test.php";s:4:"d411";s:25:"lib/smtp/test/phpunit.php";s:4:"7de8";s:22:"lib/smtp/test/test.png";s:4:"9afe";s:52:"lib/templateParser/class.tx_mailform_parseEngine.php";s:4:"bd18";s:54:"lib/templateParser/class.tx_mailform_parseVariable.php";s:4:"d811";s:55:"lib/templateParser/class.tx_mailform_templateObject.php";s:4:"1ba2";s:55:"lib/templateParser/class.tx_mailform_templateParser.php";s:4:"56b5";s:54:"lib/wizardInterface/class.tx_mailform_parentWizard.php";s:4:"eff5";s:62:"lib/wizardInterface/interface.tx_mailform_displayInterface.php";s:4:"c3fa";s:59:"lib/wizardInterface/interface.tx_mailform_mainInterface.php";s:4:"92e4";s:14:"pi1/ce_wiz.gif";s:4:"33b0";s:29:"pi1/class.tx_mailform_pi1.php";s:4:"ae70";s:37:"pi1/class.tx_mailform_pi1_wizicon.php";s:4:"79fd";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.xml";s:4:"eb7f";s:45:"pi1/mail/class.tx_mailform_emailGenerator.php";s:4:"c7e7";s:43:"pi1/mail/class.tx_mailform_sendOperator.php";s:4:"0902";s:24:"pi1/static/editorcfg.txt";s:4:"84e1";s:28:"template/icon_fatalerror.gif";s:4:"6dcc";s:20:"template/icon_ok.gif";s:4:"d103";s:26:"template/icon_required.gif";s:4:"29f1";s:21:"template/mailform.css";s:4:"1c8c";s:26:"template/mailform_back.css";s:4:"606d";s:27:"template/mailform_email.css";s:4:"79b7";s:28:"template/mailform_email.tmpl";s:4:"d41d";s:29:"template/mailform_fields.tmpl";s:4:"d397";s:34:"template/mailform_fields_back.tmpl";s:4:"c0b9";s:27:"template/mailform_page.tmpl";s:4:"625b";s:43:"tt_content_tx_mailform_config/arrowDown.gif";s:4:"6052";s:41:"tt_content_tx_mailform_config/arrowUp.gif";s:4:"81be";s:39:"tt_content_tx_mailform_config/clear.gif";s:4:"cc11";s:38:"tt_content_tx_mailform_config/conf.php";s:4:"5df5";s:37:"tt_content_tx_mailform_config/css.gif";s:4:"01c3";s:36:"tt_content_tx_mailform_config/db.gif";s:4:"ec0d";s:49:"tt_content_tx_mailform_config/doubleArrowDown.gif";s:4:"abf3";s:47:"tt_content_tx_mailform_config/doubleArrowUp.gif";s:4:"f0ae";s:39:"tt_content_tx_mailform_config/index.php";s:4:"b575";s:43:"tt_content_tx_mailform_config/javascript.js";s:4:"53bc";s:43:"tt_content_tx_mailform_config/locallang.xml";s:4:"5bf0";s:44:"tt_content_tx_mailform_config/standard_js.js";s:4:"ab17";s:45:"tt_content_tx_mailform_config/wizardStyle.css";s:4:"4f5b";s:45:"tt_content_tx_mailform_config/wizard_icon.gif";s:4:"664c";s:69:"tt_content_tx_mailform_config/model/class.tx_mailform_extendedWiz.php";s:4:"f94d";s:63:"tt_content_tx_mailform_config/model/class.tx_mailform_field.php";s:4:"a294";s:66:"tt_content_tx_mailform_config/model/class.tx_mailform_fieldWiz.php";s:4:"14dc";s:69:"tt_content_tx_mailform_config/model/class.tx_mailform_standardWiz.php";s:4:"963c";s:68:"tt_content_tx_mailform_config/model/class.tx_mailform_wizDisplay.php";s:4:"90a1";s:64:"tt_content_tx_mailform_config/model/class.tx_mailform_wizard.php";s:4:"9077";s:49:"tt_content_tx_mailform_config/overlib/makemini.pl";s:4:"e95f";s:48:"tt_content_tx_mailform_config/overlib/overlib.js";s:4:"8445";s:55:"tt_content_tx_mailform_config/overlib/overlib_anchor.js";s:4:"834e";s:60:"tt_content_tx_mailform_config/overlib/overlib_centerpopup.js";s:4:"e625";s:59:"tt_content_tx_mailform_config/overlib/overlib_crossframe.js";s:4:"e2af";s:57:"tt_content_tx_mailform_config/overlib/overlib_cssstyle.js";s:4:"0b01";s:54:"tt_content_tx_mailform_config/overlib/overlib_debug.js";s:4:"f4fb";s:58:"tt_content_tx_mailform_config/overlib/overlib_exclusive.js";s:4:"04a1";s:61:"tt_content_tx_mailform_config/overlib/overlib_followscroll.js";s:4:"2ec5";s:57:"tt_content_tx_mailform_config/overlib/overlib_hideform.js";s:4:"84d0";s:57:"tt_content_tx_mailform_config/overlib/overlib_setonoff.js";s:4:"1486";s:55:"tt_content_tx_mailform_config/overlib/overlib_shadow.js";s:4:"25a6";s:65:"tt_content_tx_mailform_config/overlib/Mini/overlib_anchor_mini.js";s:4:"c17e";s:70:"tt_content_tx_mailform_config/overlib/Mini/overlib_centerpopup_mini.js";s:4:"6e09";s:69:"tt_content_tx_mailform_config/overlib/Mini/overlib_crossframe_mini.js";s:4:"7968";s:67:"tt_content_tx_mailform_config/overlib/Mini/overlib_cssstyle_mini.js";s:4:"8ed3";s:64:"tt_content_tx_mailform_config/overlib/Mini/overlib_debug_mini.js";s:4:"840c";s:68:"tt_content_tx_mailform_config/overlib/Mini/overlib_exclusive_mini.js";s:4:"df6e";s:71:"tt_content_tx_mailform_config/overlib/Mini/overlib_followscroll_mini.js";s:4:"156c";s:67:"tt_content_tx_mailform_config/overlib/Mini/overlib_hideform_mini.js";s:4:"f31a";s:58:"tt_content_tx_mailform_config/overlib/Mini/overlib_mini.js";s:4:"8b92";s:67:"tt_content_tx_mailform_config/overlib/Mini/overlib_setonoff_mini.js";s:4:"6fc4";s:65:"tt_content_tx_mailform_config/overlib/Mini/overlib_shadow_mini.js";s:4:"c856";s:73:"tt_content_tx_mailform_config/singletons/class.tx_mailform_configData.php";s:4:"e8ad";s:74:"tt_content_tx_mailform_config/singletons/class.tx_mailform_formHandler.php";s:4:"e551";s:72:"tt_content_tx_mailform_config/singletons/class.tx_mailform_saveState.php";s:4:"1f85";s:80:"tt_content_tx_mailform_config/singletons/class.tx_mailform_tablefieldHandler.php";s:4:"1b0a";}',
);

?>