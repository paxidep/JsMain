use MOBILE_API;

CREATE TABLE `IOS_RESPONSE_LOG` (
 `PROFILEID` int(12) NOT NULL,
 `REGISTRATION_ID` varchar(255) NOT NULL,
 `MESSAGE_ID` bigint(11) NOT NULL,
 `STATUS_CODE` int(4) NOT NULL,
 `STATUS_MESSAGE` varchar(255) DEFAULT NULL,
 `SEND_DATE` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
 `NOTIFICATION_KEY` varchar(20) NOT NULL,
 KEY `MESSAGE_ID` (`MESSAGE_ID`),
 KEY `SEND_DATE` (`SEND_DATE`),
 KEY `PROFILEID` (`PROFILEID`)
)ENGINE=InnoDB;

drop table `NOTIFICATION_LOG`;

CREATE TABLE `NOTIFICATION_LOG` (
 `PROFILEID` int(11) DEFAULT NULL,
 `NOTIFICATION_KEY` varchar(20) DEFAULT NULL,
 `MESSAGE_ID` bigint(11) DEFAULT NULL,
 `SEND_DATE` datetime DEFAULT NULL,
 `SENT` char(1) NOT NULL,
 `OS_TYPE` char(1) DEFAULT NULL,
 KEY `PROFILEID` (`PROFILEID`),
 KEY `SEND_DATE` (`SEND_DATE`),
 KEY `NOTIFICATION_KEY` (`NOTIFICATION_KEY`),
 KEY `MESSAGE_ID` (`MESSAGE_ID`)
)ENGINE=InnoDB;

UPDATE MOBILE_API.APP_NOTIFICATIONS SET TIME_CRITERIA='' WHERE NOTIFICATION_KEY='ACCEPTANCE';
UPDATE MOBILE_API.APP_NOTIFICATIONS SET OS_TYPE='AND' WHERE NOTIFICATION_KEY IN('VD','CSV_UPLOAD');
