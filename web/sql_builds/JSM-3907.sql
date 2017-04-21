use search;

CREATE TABLE  `PIADMEMBERS_TO_BE_SENT` (
 `PROFILEID` INT( 11 ) UNSIGNED NOT NULL DEFAULT  '0',
 `IS_CALCULATED` ENUM(  'N',  'Y' ) NOT NULL DEFAULT  'N',
PRIMARY KEY (  `PROFILEID` )
) ENGINE = MYISAM;

	CREATE TABLE `PAID_MEMBERS_MAILER` (
 `RECEIVER` int(11) unsigned DEFAULT '0',
 `USER1` int(11) unsigned DEFAULT '0',
 `USER2` int(11) unsigned DEFAULT '0',
 `USER3` int(11) unsigned DEFAULT '0',
 `USER4` int(11) unsigned DEFAULT '0',
 `USER5` int(11) unsigned DEFAULT '0',
 `USER6` int(11) unsigned DEFAULT '0',
 `USER7` int(11) unsigned DEFAULT '0',
 `USER8` int(11) unsigned DEFAULT '0',
 `USER9` int(11) unsigned DEFAULT '0',
 `USER10` int(11) unsigned DEFAULT '0',
 `USER11` int(11) unsigned DEFAULT '0',
 `USER12` int(11) unsigned DEFAULT '0',
 `USER13` int(11) unsigned DEFAULT '0',
 `USER14` int(11) unsigned DEFAULT '0',
 `USER15` int(11) unsigned DEFAULT '0',
 `USER16` int(11) unsigned DEFAULT '0',
 `SNO` mediumint(11) NOT NULL AUTO_INCREMENT,
 `SENT` varchar(1) NOT NULL DEFAULT 'N',
 PRIMARY KEY (`SNO`),
 KEY `SENT` (`SENT`),
) ENGINE=MYISAM;

use newjs;
ALTER TABLE `SEARCH_MALE` ADD `PAID_ON` DATETIME DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `SEARCH_FEMALE` ADD `PAID_ON` DATETIME DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `SWAP` ADD `PAID_ON` DATETIME DEFAULT '0000-00-00 00:00:00';

use jeevansathi_mailer;
INSERT INTO  `LINK_MAILERS` (  `LINKID` ,  `APP_SCREEN_ID` ,  `LINK_NAME` ,  `LINK_URL` ,  `OTHER_GET_PARAMS` ,  `REQUIRED_AUTOLOGIN` ,  `OUTER_LINK` ) 
VALUES (
'72', NULL ,  'PAID_MEMBERS_MAILER', NULL , NULL ,  'Y',  'N'
);