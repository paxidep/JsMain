use PICTURE;
CREATE TABLE `INCORRECT_PICTURE_DATA` (
 `PICTUREID` int(11) unsigned NOT NULL DEFAULT '0',
 `PROFILEID` int(11) unsigned DEFAULT '0',
 `ORDERING` int(1) DEFAULT NULL,
 `REASON` varchar(2) DEFAULT NULL,
 PRIMARY KEY (`PICTUREID`)
) ENGINE=MYISAM;