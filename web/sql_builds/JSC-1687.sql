use billing;

CREATE TABLE `VD_CLUSTER` (
 `CLUSTER` varchar(40) NOT NULL DEFAULT '',
 `CRITERIA` varchar(200) NOT NULL DEFAULT '',
 `VALUE1` char(20) NOT NULL DEFAULT '',
 `VALUE2` char(20) NOT NULL DEFAULT '',
 `ENTRY_DT` datetime NOT NULL,
 UNIQUE KEY `CLUSTER` (`CLUSTER`,`CRITERIA`),
 KEY `ENTRY_DT` (`ENTRY_DT`)
) ENGINE=MyISAM
