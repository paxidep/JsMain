use incentive;

CREATE TABLE  `FTA_ALLOCATION_TECH` (`ID` MEDIUMINT( 9 ) NOT NULL AUTO_INCREMENT ,`PROFILEID` INT( 10 ) UNSIGNED NOT NULL ,`ALLOTED_TO` VARCHAR( 50 ) NOT NULL ,`HANDLED` CHAR( 1 ) NOT NULL ,`ALLOT_DT` DATE NOT NULL ,`PROFILE_TYPE` CHAR( 1 ) NOT NULL ,PRIMARY KEY (  `ID` ));
