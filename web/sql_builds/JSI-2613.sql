use MOBILE_API;
ALTER TABLE  `MATCH_OF_DAY_LOG` ADD  `IGNORE` CHAR( 1 ) DEFAULT  'N' NOT NULL AFTER  `ENTRY_DT`;