use incentive;

ALTER TABLE incentive.SALES_CSV_DATA_FAILED_PAYMENT ADD SOURCE varchar(20) AFTER DIAL_STATUS;
ALTER TABLE incentive.SALES_CSV_DATA_FAILED_PAYMENT drop index PROFILEID;
ALTER TABLE incentive.SALES_CSV_DATA_FAILED_PAYMENT ADD UNIQUE(`PROFILEID`,`DIAL_STATUS`,`SOURCE`);
