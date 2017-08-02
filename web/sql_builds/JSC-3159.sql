use billing;

CREATE TABLE billing.`EXCLUSIVE_FOLLOWUPS` (
 `ID` int(11) NOT NULL AUTO_INCREMENT,
 `AGENT_USERNAME` varchar(20) NOT NULL,
 `CLIENT_ID` int(11) NOT NULL,
 `MEMBER_ID` int(11) NOT NULL,
 `ENTRY_DT` datetime DEFAULT NULL,
 `FOLLOWUP_1` varchar(100) DEFAULT NULL,
 `FOLLOWUP_2` varchar(100) DEFAULT NULL,
 `FOLLOWUP_3` varchar(100) DEFAULT NULL,
 `FOLLOWUP_4` varchar(100) DEFAULT NULL,
 `FOLLOWUP1_DT` date DEFAULT NULL,
 `FOLLOWUP2_DT` date DEFAULT NULL,
 `FOLLOWUP3_DT` date DEFAULT NULL,
 `FOLLOWUP4_DT` date DEFAULT NULL,
 `STATUS` enum('F0','Y','N','F1','F2','F3','F4') DEFAULT 'F0',
 `CONCALL_SCH_DT` date DEFAULT NULL,
 `CONCALL_STATUS` enum('N','Y') DEFAULT 'N',
 `CONCALL_ACTUAL_DT` datetime DEFAULT NULL,
 PRIMARY KEY (`ID`),
 KEY `STATUS` (`STATUS`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
