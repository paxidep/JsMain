use duplicates;

CREATE TABLE `DUPLICATE_CHECKS_FIELDS` (
 `PROFILEID` int(11) NOT NULL,
 `TYPE` enum('NEW','EDIT') DEFAULT NULL,
 `FIELDS_TO_BE_CHECKED` bigint(20) DEFAULT NULL,
 `TIMESTAMP` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`PROFILEID`)
) ENGINE=MYISAM;
