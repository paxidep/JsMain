use billing;

CREATE TABLE `PAYMENT_HITS_REVAMP` (
 `ID` int(11) NOT NULL AUTO_INCREMENT,
 `PROFILEID` int(11) NOT NULL DEFAULT '0',
 `PAGE` int(2) NOT NULL DEFAULT '0',
 `ENTRY_DT` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
 `TAB_BUTTON` int(2) DEFAULT '0',
 PRIMARY KEY (`ID`),
 KEY `PROFILEID` (`PROFILEID`),
 KEY `ENTRY_DT` (`ENTRY_DT`)
) ENGINE=MyISAM;

insert into billing.PAYMENT_HITS_REVAMP(`PROFILEID`,`PAGE`,`ENTRY_DT`) select PROFILEID,PAGE,ENTRY_DT from billing.PAYMENT_HITS where ENTRY_DT>'2013-08-01 00:00:00';

RENAME TABLE `billing.PAYMENT_HITS` TO `billing.PAYMENT_HITS_BACKUP_20130923`;
RENAME TABLE `billing.PAYMENT_HITS_REVAMP` TO `billing.PAYMENT_HITS`;

CREATE TABLE `TRACKING_BILLING_REVAMP` (
 `ID` int(11) NOT NULL AUTO_INCREMENT,
 `PROFILEID` int(11) unsigned NOT NULL,
 `USER_TYPE` varchar(60) NULL DEFAULT '',
 `SERVICE_SELECTED` varchar(100) NULL DEFAULT '',
 `ENTRY_DT` date NULL DEFAULT '0000-00-00',
 `NET_AMOUNT` tinyint(4) NULL DEFAULT 0,
 `DISCOUNT` tinyint(4) NULL DEFAULT 0,
 `TAB_BUTTON` varchar(60) NULL DEFAULT '',
 `NAV_SERVICE_SUGGESTED` varchar(200) NULL DEFAULT '',
 `VAS_SUGGESTED_SELECTED` varchar(50) NULL DEFAULT '',
 PRIMARY KEY (`ID`),
 KEY `PROFILEID` (`PROFILEID`),
 KEY `ENTRY_DT` (`ENTRY_DT`)
);
