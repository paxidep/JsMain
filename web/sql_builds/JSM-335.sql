use jsadmin;

ALTER TABLE  `PROFILE_CHANGE_REQUEST` ADD  `ORIG_RELIGION` TINYINT UNSIGNED,
ADD  `NEW_RELIGION` TINYINT UNSIGNED,
ADD  `ORIG_CASTE` SMALLINT UNSIGNED,
ADD  `NEW_CASTE` SMALLINT UNSIGNED;