use newjs;
ALTER TABLE `SEARCH_MALE` CHANGE `LAST_LOGIN_DT` `LAST_LOGIN_DT` DATETIME DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `SEARCH_FEMALE` CHANGE `LAST_LOGIN_DT` `LAST_LOGIN_DT` DATETIME DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `SWAP` CHANGE `LAST_LOGIN_DT` `LAST_LOGIN_DT` DATETIME DEFAULT '0000-00-00 00:00:00';