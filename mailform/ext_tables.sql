#
# Table structure for table 'tt_content'
#
CREATE TABLE tt_content (
	keywords varchar(80) DEFAULT '' NOT NULL,
	tx_mailform_config longtext NOT NULL,
	tx_mailform_changed int(1) DEFAULT '0' NOT NULL
);

CREATE TABLE tx_mailform_fileHandler (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,

	filecode text,
	db_expire int(18) unsigned DEFAULT '0' NOT NULL,	
	file_expired int(1) unsigned DEFAULT '0' NOT NULL,
	user_sess_id varchar(256) DEFAULT '' NOT NULL,
	field_id varchar(128) DEFAULT '' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

