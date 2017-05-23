CREATE DATABASE IMAGE_SERVER;
USE IMAGE_SERVER;
CREATE TABLE LOG (
  AUTOID int(20) unsigned NOT NULL AUTO_INCREMENT,
  MODULE_NAME varchar(30) DEFAULT NULL,
  MODULE_ID int(11) unsigned DEFAULT NULL,
  IMAGE_TYPE varchar(10) DEFAULT NULL,
  STATUS enum('Y','N','D') NOT NULL DEFAULT 'N',
  DATE date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (AUTOID),
  UNIQUE KEY MODULE_ID (MODULE_NAME,MODULE_ID,IMAGE_TYPE),
  KEY STATUS (STATUS)
) ENGINE=myisam;
set session sql_log_bin=0;
alter table LOG engine=innodb;
set session sql_log_bin=1;
