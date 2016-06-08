USE billing;

UPDATE billing.SERVICES SET PRICE_RS_TAX=3800 WHERE SERVICEID='P3';
UPDATE billing.SERVICES SET PRICE_RS_TAX=5650 WHERE SERVICEID='P6';
UPDATE billing.SERVICES SET PRICE_RS_TAX=8900 WHERE SERVICEID='P12';
UPDATE billing.SERVICES SET PRICE_RS_TAX=11100 WHERE SERVICEID='PL';
UPDATE billing.SERVICES SET PRICE_RS_TAX=4400 WHERE SERVICEID='C3';
UPDATE billing.SERVICES SET PRICE_RS_TAX=7050 WHERE SERVICEID='C6';
UPDATE billing.SERVICES SET PRICE_RS_TAX=10700 WHERE SERVICEID='C12';
UPDATE billing.SERVICES SET PRICE_RS_TAX=13100 WHERE SERVICEID='CL';
UPDATE billing.SERVICES SET PRICE_RS_TAX=5300 WHERE SERVICEID='NCP3';
UPDATE billing.SERVICES SET PRICE_RS_TAX=8050 WHERE SERVICEID='NCP6';
UPDATE billing.SERVICES SET PRICE_RS_TAX=12700 WHERE SERVICEID='NCP12';

UPDATE SERVICES SET PRICE_RS=ROUND(PRICE_RS_TAX/1.145,2) WHERE SERVICEID IN ('P3','P6','P12','PL','C3','C6','C12','CL','NCP3','NCP6','NCP12');

UPDATE billing.SERVICES SET desktop_RS = PRICE_RS_TAX, mobile_website_RS = PRICE_RS_TAX, JSAA_mobile_website_RS = PRICE_RS_TAX, old_mobile_website_RS = PRICE_RS_TAX, Android_app_RS = PRICE_RS_TAX;