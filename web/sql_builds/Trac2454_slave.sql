CREATE DATABASE  `new_matches_emails` ;

GRANT SELECT, INSERT, DELETE,UPDATE ON new_matches_emails TO 'alerts';

FLUSH PRIVILEGES;

use new_matches_emails;

CREATE TABLE `RECEIVER` (
  `PROFILEID` int(11) unsigned NOT NULL,
  `SENT` char(1) DEFAULT NULL,
  PRIMARY KEY (`PROFILEID`),
  KEY `SENT` (`SENT`)
) ENGINE=MyISAM;

CREATE TABLE ZEROTvNEW LIKE matchalerts.ZEROTvNT;

CREATE TABLE ZERONTvNEW LIKE matchalerts.ZEROTvNT;

CREATE TABLE GENDER_OR_JPARTNER_ERROR LIKE matchalerts.ZEROTvNT;

CREATE TABLE MAILER LIKE matchalerts.MAILER;

CREATE TABLE LOG_TEMP LIKE matchalerts.LOG_TEMP;

CREATE TABLE LOG LIKE LOG_TEMP;

ALTER TABLE  `MAILER` DROP  `IS_USER_ACTIVE` ,
DROP  `RECOMEND_USER1` ,
DROP  `RECOMEND_USER2` ,
DROP  `RECOMEND_USER3` ,
DROP  `RECOMEND_USER4` ,
DROP  `RECOMEND_USER5` ,
DROP  `RECOMEND_USER6` ,
DROP  `RECOMEND_USER7` ,
DROP  `RECOMEND_USER8` ,
DROP  `RECOMEND_USER9` ,
DROP  `RECOMEND_USER10` ,
DROP  `FREQUENCY` ;

ALTER TABLE  `MAILER` ADD  `LINK_REQUIRED` CHAR( 1 ) NOT NULL ;

ALTER TABLE  `MAILER` ADD  `RELAX_CRITERIA` VARCHAR( 20 );

CREATE TABLE TOP_VIEW_COUNT LIKE matchalerts.TOP_VIEW_COUNT;

ALTER TABLE  `TOP_VIEW_COUNT` DROP  `FREQUENCY`;

CREATE TABLE TOP_VIEW_COUNT_BKUP LIKE TOP_VIEW_COUNT;