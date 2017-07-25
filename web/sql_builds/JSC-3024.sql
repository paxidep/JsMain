use billing;

CREATE TABLE billing.`EXCLUSIVE_SERVICING` (
 `ID` int(11) NOT NULL AUTO_INCREMENT,
 `AGENT_USERNAME` varchar(20) NOT NULL,
 `CLIENT_ID` int(11) NOT NULL,
 `ASSIGNED_DT` date NOT NULL,
 `ENTRY_DT` datetime DEFAULT NULL,
 `SERVICE_DAY` enum('NA','SUN','MON','TUE','WED','THUR','FRI','SAT') DEFAULT 'NA',
 `SERVICE_SET_DT` datetime DEFAULT "0000-00-00 00:00:00",
 `BIODATA_LOCATION` varchar(100) DEFAULT NULL,
 `BIODATA_UPLOAD_DT` datetime DEFAULT "0000-00-00 00:00:00",
 `SCREENED_DT` date DEFAULT "0000-00-00",
 `SCREENED_STATUS` enum('Y', 'N') DEFAULT 'N',
 `EMAIL_STAGE` varchar(1) DEFAULT NULL,
 PRIMARY KEY (`ID`),
 UNIQUE COMBINATION(`AGENT_USERNAME`,`CLIENT_ID`,`ASSIGNED_DT`),
 KEY AGENT_USERNAME(`AGENT_USERNAME`),
 KEY CLIENT_ID(`CLIENT_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE billing.`EXCLUSIVE_CLIENT_MEMBER_MAPPING` (
 `ID` int(11) NOT NULL AUTO_INCREMENT,
 `CLIENT_ID` int(11) NOT NULL,
 `MEMBER_ID` int(11) NOT NULL,
 `ENTRY_DT` datetime DEFAULT NULL,
 `SCREENED_STATUS` enum('Y', 'N','P','E') DEFAULT 'N',
 PRIMARY KEY (`ID`),
 KEY CLIENT_ID(`CLIENT_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO jeevansathi_mailer.`EMAIL_TYPE` (`MAIL_ID`, `TPL_LOCATION`, `HEADER_TPL`, `FOOTER_TPL`, `TEMPLATE_EX_LOCATION`, `MAIL_GROUP`, `CUSTOM_CRITERIA`, `SENDER_EMAILID`, `DESCRIPTION`, `MEMBERSHIP_TYPE`, `GENDER`, `PHOTO_PROFILE`, `REPLY_TO_ENABLED`, `FROM_NAME`, `REPLY_TO_ADDRESS`, `MAX_COUNT_TO_BE_SENT`, `REQUIRE_AUTOLOGIN`, `FTO_FLAG`, `PRE_HEADER`, `PARTIALS`) VALUES (1857, 'jsExclusiveServiceDayMailer.tpl', NULL, 'revamp_footer.tpl', NULL, 27, 1, '~$senderEmail`', 'Upgrade membership to JS Exclusive', 'D', NULL, NULL, NULL, '~$senderName`', NULL, NULL, NULL, NULL, 'Please add ~$senderEmail` to your address book to ensure delivery of this mail into you inbox', '');

