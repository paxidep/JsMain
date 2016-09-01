USE incentive;

CREATE TABLE incentive.INBOUND_SALES_LOG (
ID INT( 11 ) NOT NULL AUTO_INCREMENT ,
CAMPAIGN_NAME VARCHAR( 50 ) NOT NULL ,
CALL_COUNT INT( 11 ) DEFAULT '0' NOT NULL ,
ENTRY_DT DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL ,
PRIMARY KEY ( ID ) ,
INDEX ( CAMPAIGN_NAME , ENTRY_DT )
);

INSERT INTO MIS.MIS_MAINPAGE VALUES (NULL, "Inbound Sales Campaign MIS", "/operations.php/crmMis/inboundSalesCampaignMis?name=$user&cid=$cid", '', 'MC1', 'Y', '');