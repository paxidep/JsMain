use Assisted_Product;

ALTER TABLE `AP_TEMP_DPP` ADD `LINCOME` CHAR( 2 ) NOT NULL , ADD `HINCOME` CHAR( 2 ) NOT NULL , ADD `LINCOME_DOL` CHAR( 2 ) NOT NULL , ADD
`HINCOME_DOL` CHAR( 2 ) NOT NULL ;

ALTER TABLE `AP_DPP_FILTER_ARCHIVE` ADD `LINCOME` CHAR( 2 ) NOT NULL , ADD `HINCOME` CHAR( 2 ) NOT NULL , ADD `LINCOME_DOL` CHAR( 2 ) NOT NULL ,
ADD `HINCOME_DOL` CHAR( 2 ) NOT NULL ;