#
# Table structure for table 'tx_mailformstatistics_settings'
#
CREATE TABLE tx_mailformstatistics_settings (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

CREATE TABLE tx_mailformstatistics_mails (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	title varchar(245) DEFAULT '' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	formid int(11) unsigned DEFAULT '0' NOT NULL,
	mailid int(11) unsigned DEFAULT '0' NOT NULL,
	recipient text NOT NULL,
	recipient_admin text NOT NULL,
	subject varchar(245) DEFAULT '' NOT NULL,
	REMOTE_ADDR varchar(60) DEFAULT '' NOT NULL,
	HTTP_USER_AGENT varchar(255) DEFAULT '' NOT NULL,
	REMOTE_PORT int(11) unsigned DEFAULT '0' NOT NULL
	PRIMARY KEY (uid),
	KEY parent (pid)
);

CREATE TABLE tx_mailformstatistics_stats (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,
	tstamp int(14) unsigned DEFAULT '0' NOT NULL,
	crdate int(14) unsigned DEFAULT '0' NOT NULL,
	mailid int(11) unsigned DEFAULT '0' NOT NULL,
	ufid varchar(50) DEFAULT '0' NOT NULL,
	content_blob blob NOT NULL,
	content_text text NOT NULL,
	content_varchar varchar(245) DEFAULT '' NOT NULL,
	content_int int(18) unsigned DEFAULT '0' NOT NULL,
	field_type varchar(80) DEFAULT 'default' NOT NULL,
	data_type varchar(40) DEFAULT 'empty' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);