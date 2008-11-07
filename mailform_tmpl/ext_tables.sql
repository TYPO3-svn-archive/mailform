
CREATE TABLE tx_mailformtmpl_settings (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	title varchar(245) DEFAULT '' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	settings_key varchar(70) DEFAULT '' NOT NULL,
	settings_value varchar(100) DEFAULT '' NOT NULL,
	PRIMARY KEY (uid),
	KEY parent (pid)
);

CREATE TABLE tx_mailformtmpl_history {
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	title varchar(245) DEFAULT '' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	plid int(11) unsigned DEFAULT '0' NOT NULL,
	saved int(17) unsigned DEFAULT '0' NOT NULL,
	xml_element blob NOT NULL,
	PRIMARY KEY (uid),
	KEY parent (pid)
);

