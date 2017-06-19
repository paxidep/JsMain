use billing;

CREATE TABLE billing.`DISCOUNT_HISTORY_MAX` (
 `ID` int(11) NOT NULL AUTO_INCREMENT,
 `PROFILEID` int(11) NOT NULL,
 `LAST_LOGIN_DATE` date NOT NULL,
 `P` tinyint(3) NOT NULL,
 `C` tinyint(3) NOT NULL,
 `NCP` tinyint(3) NOT NULL,
 `X` tinyint(3) NOT NULL,
 PRIMARY KEY (`ID`),
 UNIQUE KEY `PROFILE_UNIQUE` (`PROFILEID`),
 KEY `PROFILEID` (`PROFILEID`),
 KEY `LAST_LOGIN_DATE` (`LAST_LOGIN_DATE`)
) ENGINE=InnoDB;