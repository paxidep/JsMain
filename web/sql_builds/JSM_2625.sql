use mailer;
CREATE TABLE `FEATURED_PROFILE_MAILER` (
 `SNO` mediumint(11) unsigned NOT NULL AUTO_INCREMENT,
 `PROFILEID` int(11) unsigned NOT NULL,
 `SENT` varchar(1) DEFAULT 'N',
 PRIMARY KEY (`SNO`),
 UNIQUE KEY `PROFILEID` (`PROFILEID`)
) ENGINE=MYISAM;