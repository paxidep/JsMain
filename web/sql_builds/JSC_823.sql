USE billing;

UPDATE billing.PURCHASES SET SERVICE_TAX_CONTENT='(Inclusive of Swachh Bharat Cess)' WHERE CUR_TYPE='RS' AND ENTRY_DT>'2015-11-14 23:59:59'