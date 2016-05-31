use billing;

CREATE TABLE `FESTIVE_LOG_REVAMP` (
 `ID` mediumint(11) NOT NULL AUTO_INCREMENT,
 `EXECUTIVE` varchar(120) NOT NULL,
 `STATUS` enum('Active','Inactive') NOT NULL,
 `START_DT` date DEFAULT '0000-00-00',
 `END_DT` date DEFAULT '0000-00-00',
 `LAST_ACTIVE_SERVICES` varchar(60) DEFAULT NULL,
 `FESTIVAL` tinyint(3) DEFAULT '0',
 `ACTIVATION_DT` datetime DEFAULT '0000-00-00 00:00:00',
 `DE_ACTIVATION_DT` datetime DEFAULT '0000-00-00 00:00:00',
 PRIMARY KEY (`ID`)
) ENGINE=InnoDB

CREATE TABLE `DISCOUNT_OFFER` (
 `ID` mediumint(11) NOT NULL AUTO_INCREMENT,
 `SERVICEID` varchar(10) DEFAULT NULL,
 `DISCOUNT` mediumint(2) DEFAULT NULL,
 PRIMARY KEY (`ID`)
) 

CREATE TABLE `DISCOUNT_OFFER_LOG` (
 `ID` mediumint(11) NOT NULL AUTO_INCREMENT,
 `EXECUTIVE` varchar(60) NOT NULL,
 `STATUS` enum('Y','N') NOT NULL,
 `START_DT` date DEFAULT '0000-00-00',
 `END_DT` date DEFAULT '0000-00-00',
 `ACTIVATION_DT` datetime DEFAULT '0000-00-00 00:00:00',
 `DE_ACTIVATION_DT` datetime DEFAULT '0000-00-00 00:00:00',
 PRIMARY KEY (`ID`)
)

CREATE TABLE `FESTIVE_BANNER` (
 `ID` mediumint(11) NOT NULL AUTO_INCREMENT,
 `FESTIVAL` varchar(60) NOT NULL,
 `IMAGE_URL` varchar(200) NOT NULL,
 PRIMARY KEY (`ID`)
) ENGINE=MyISAM

INSERT INTO `FESTIVE_BANNER` (`ID`, `FESTIVAL`, `IMAGE_URL`) VALUES (1, 'Generic', 'generic_banner.jpg');
INSERT INTO `FESTIVE_BANNER` (`ID`, `FESTIVAL`, `IMAGE_URL`) VALUES (2, 'New Year', '');
INSERT INTO `FESTIVE_BANNER` (`ID`, `FESTIVAL`, `IMAGE_URL`) VALUES (3, 'Makar Sankranti', '');
INSERT INTO `FESTIVE_BANNER` (`ID`, `FESTIVAL`, `IMAGE_URL`) VALUES (4, 'Republic Day', '');
INSERT INTO `FESTIVE_BANNER` (`ID`, `FESTIVAL`, `IMAGE_URL`) VALUES (5, 'Valentine''s Day', '');
INSERT INTO `FESTIVE_BANNER` (`ID`, `FESTIVAL`, `IMAGE_URL`) VALUES (6, 'Vasant Panchami', '');
INSERT INTO `FESTIVE_BANNER` (`ID`, `FESTIVAL`, `IMAGE_URL`) VALUES (7, 'Maha Shivratri', '');
INSERT INTO `FESTIVE_BANNER` (`ID`, `FESTIVAL`, `IMAGE_URL`) VALUES (8, 'Holi', '');
INSERT INTO `FESTIVE_BANNER` (`ID`, `FESTIVAL`, `IMAGE_URL`) VALUES (9, 'Good Friday', '');
INSERT INTO `FESTIVE_BANNER` (`ID`, `FESTIVAL`, `IMAGE_URL`) VALUES (10, 'Ram Navami', '');
INSERT INTO `FESTIVE_BANNER` (`ID`, `FESTIVAL`, `IMAGE_URL`) VALUES (11, 'Mahavir Jayanti', '');
INSERT INTO `FESTIVE_BANNER` (`ID`, `FESTIVAL`, `IMAGE_URL`) VALUES (12, 'May Day', '');
INSERT INTO `FESTIVE_BANNER` (`ID`, `FESTIVAL`, `IMAGE_URL`) VALUES (13, 'Mother''s Day', '');
INSERT INTO `FESTIVE_BANNER` (`ID`, `FESTIVAL`, `IMAGE_URL`) VALUES (14, 'Father''s Day', '');
INSERT INTO `FESTIVE_BANNER` (`ID`, `FESTIVAL`, `IMAGE_URL`) VALUES (15, 'Independence Day', '');
INSERT INTO `FESTIVE_BANNER` (`ID`, `FESTIVAL`, `IMAGE_URL`) VALUES (16, 'Raksha Bandhan', '');
INSERT INTO `FESTIVE_BANNER` (`ID`, `FESTIVAL`, `IMAGE_URL`) VALUES (17, 'Janmashtami', '');
INSERT INTO `FESTIVE_BANNER` (`ID`, `FESTIVAL`, `IMAGE_URL`) VALUES (18, 'Ganesh Chaturthi', '');
INSERT INTO `FESTIVE_BANNER` (`ID`, `FESTIVAL`, `IMAGE_URL`) VALUES (19, 'Gandhi Jayanti', '');
INSERT INTO `FESTIVE_BANNER` (`ID`, `FESTIVAL`, `IMAGE_URL`) VALUES (20, 'Dussehra', '');
INSERT INTO `FESTIVE_BANNER` (`ID`, `FESTIVAL`, `IMAGE_URL`) VALUES (21, 'Eid', '');
INSERT INTO `FESTIVE_BANNER` (`ID`, `FESTIVAL`, `IMAGE_URL`) VALUES (22, 'Diwali', '');
INSERT INTO `FESTIVE_BANNER` (`ID`, `FESTIVAL`, `IMAGE_URL`) VALUES (23, 'Bhai Duj', '');
INSERT INTO `FESTIVE_BANNER` (`ID`, `FESTIVAL`, `IMAGE_URL`) VALUES (24, 'Guru Nanak Jayanti', '');
INSERT INTO `FESTIVE_BANNER` (`ID`, `FESTIVAL`, `IMAGE_URL`) VALUES (25, 'Christmas', '');

