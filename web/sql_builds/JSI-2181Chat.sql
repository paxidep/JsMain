USE newjs;

ALTER TABLE  `CHAT_LOG` CHANGE  `ID`  `ID` INT( 11 ) NULL AUTO_INCREMENT;

ALTER TABLE  `CHATS` CHANGE  `ID`  `ID` VARCHAR( 100 ) NOT NULL;
