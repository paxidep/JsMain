use test;
CREATE TABLE `LAST_LOGIN_PROFILES` (
  `PROFILEID` mediumint(9) NOT NULL DEFAULT '0',
  `DATE` date NOT NULL DEFAULT '0000-00-00',
  KEY `PROFILEID` (`PROFILEID`)
) ENGINE=MyISAM;