use billing;

CREATE TABLE `QA_ONLINE_CSV_DATA` (
 `ID` int(11) NOT NULL AUTO_INCREMENT,
 `PROFILEID` int(11) NOT NULL,
 `SCORE` tinyint(3) DEFAULT '0',
 `EMAIL` varchar(50) DEFAULT '',
 `MOB_NO` int(12) DEFAULT '0',
 `RES_NO` int(12) DEFAULT '0',
 `ENTRY_DT` date NOT NULL DEFAULT '0000-00-00',
 `TYPE` enum('N','R') DEFAULT 'N',
 PRIMARY KEY (`ID`),
 KEY `PROFILEID` (`PROFILEID`),
 KEY `ENTRY_DT` (`ENTRY_DT`)
)ENGINE=InnoDB; 