use incentive;

ALTER TABLE incentive.IN_DIALER ADD COLUMN CAMPAIGN_NAME varchar(30) NOT NULL DEFAULT '' AFTER PRIORITY; 

CREATE TABLE incentive.`IN_DIALER_NEW` (
 `PROFILEID` int(11) NOT NULL,
 `ELIGIBLE` char(2) NOT NULL DEFAULT 'Y',
 `PRIORITY` tinyint(2) DEFAULT NULL,
 `CAMPAIGN_NAME` varchar(30) NOT NULL DEFAULT '',
 `ENTRY_DATE` date DEFAULT '0000-00-00',
 PRIMARY KEY (`PROFILEID`)
) ENGINE=InnoDB;

CREATE TABLE incentive.`SALES_CSV_DATA_NOIDA_NEW` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `PROFILEID` int(10) NOT NULL,
  `PRIORITY` tinyint(2) DEFAULT '0',
  `ANALYTIC_SCORE` tinyint(3) DEFAULT '0',
  `OLD_PRIORITY` tinyint(2) DEFAULT '0',
  `DIAL_STATUS` tinyint(1) DEFAULT '0',
  `AGENT` varchar(60) DEFAULT '',
  `VD_PERCENT` tinyint(3) DEFAULT '0',
  `LAST_LOGIN_DATE` date DEFAULT '0000-00-00',
  `PHONE_NO1` varchar(12) DEFAULT '',
  `PHONE_NO2` varchar(12) DEFAULT '',
  `PHOTO` char(3) DEFAULT '',
  `DOB` date DEFAULT '0000-00-00',
  `MSTATUS` varchar(40) DEFAULT '',
  `EVER_PAID` char(3) DEFAULT '',
  `GENDER` char(6) DEFAULT '',
  `POSTEDBY` varchar(40) DEFAULT '',
  `NEW_VARIABLE1` varchar(20) DEFAULT '',
  `NEW_VARIABLE2` varchar(20) DEFAULT '',
  `NEW_VARIABLE3` varchar(20) DEFAULT '',
  `PHONE_NO3` varchar(12) NOT NULL DEFAULT '',
  `PHONE_NO4` varchar(12) DEFAULT '',
  `LEAD_ID` varchar(30) DEFAULT '',
  `CSV_ENTRY_DATE` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `PROFILEID` (`PROFILEID`,`CSV_ENTRY_DATE`)
) ENGINE=InnoDB; 
 

