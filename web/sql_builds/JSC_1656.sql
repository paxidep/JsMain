use incentive;

CREATE TABLE incentive.BACKEND_DISCOUNT_SENT_LOG (
AGENT_NAME VARCHAR( 50 ) ,
USERNAME VARCHAR( 50 ) ,
PROFILEID INT( 11 ) ,
PLANS_SELECTED VARCHAR( 50 ) ,
CURRENCY VARCHAR( 5 ) ,
BASE_AMOUNT INT( 11 ) DEFAULT '0' NOT NULL ,
PAYABLE_AMOUNT INT( 11 ) DEFAULT '0' NOT NULL ,
ENTRY_DT DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL ,
INDEX AGENT_NAME( AGENT_NAME ), INDEX PROFILEID (PROFILEID), INDEX ENTRY_DT (ENTRY_DT)
);