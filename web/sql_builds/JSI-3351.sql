use feedback;
create table ABUSE_ATTACHMENTS ( 
ID BIGINT AUTO_INCREMENT PRIMARY KEY, 
DOC_1 VARCHAR(255) NOT NULL DEFAULT '', 
DOC_2 VARCHAR(255) NOT NULL DEFAULT '', 
DOC_3 VARCHAR(255) NOT NULL DEFAULT '', 
DOC_4 VARCHAR(255) NOT NULL DEFAULT '', 
DOC_5 VARCHAR(255) NOT NULL DEFAULT '') ENGINE=INNODB;

create table ABUSE_TEMP_ATTACHMENTS like ABUSE_ATTACHMENTS;

ALTER TABLE REPORT_ABUSE_LOG ADD COLUMN ATTACHMENT_ID BIGINT(20) NOT NULL DEFAULT -1;
ALTER TABLE  `REPORT_ABUSE_LOG` ADD INDEX (  `ATTACHMENT_ID` )