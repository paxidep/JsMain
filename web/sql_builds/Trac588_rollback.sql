<?php

use database: `billing`

UPDATE SERVICES SET SHOW_ONLINE='Y' WHERE SERVICEID='P2' LIMIT 1;
UPDATE SERVICES SET SHOW_ONLINE='N' WHERE SERVICEID='P3' LIMIT 1;

UPDATE SERVICES SET SHOW_ONLINE='Y' WHERE SERVICEID='C2' LIMIT 1;
UPDATE SERVICES SET SHOW_ONLINE='N' WHERE SERVICEID='C3' LIMIT 1;

UPDATE SERVICES SET SHOW_ONLINE='Y' WHERE SERVICEID='T2' LIMIT 1;
UPDATE SERVICES SET SHOW_ONLINE='Y' WHERE SERVICEID='A2' LIMIT 1;
UPDATE SERVICES SET SHOW_ONLINE='Y' WHERE SERVICEID='M2' LIMIT 1;
UPDATE SERVICES SET SHOW_ONLINE='Y' WHERE SERVICEID='B2' LIMIT 1;

UPDATE SERVICES SET SHOW_ONLINE='Y' WHERE SERVICEID='T12' LIMIT 1;
UPDATE SERVICES SET SHOW_ONLINE='Y' WHERE SERVICEID='A12' LIMIT 1;
UPDATE SERVICES SET SHOW_ONLINE='Y' WHERE SERVICEID='M12' LIMIT 1;
UPDATE SERVICES SET SHOW_ONLINE='Y' WHERE SERVICEID='B12' LIMIT 1;

UPDATE SERVICES SET SHOW_ONLINE='N' WHERE SERVICEID='T3' LIMIT 1;
UPDATE SERVICES SET SHOW_ONLINE='N' WHERE SERVICEID='A3' LIMIT 1;
UPDATE SERVICES SET SHOW_ONLINE='N' WHERE SERVICEID='M3' LIMIT 1;
UPDATE SERVICES SET SHOW_ONLINE='N' WHERE SERVICEID='B3' LIMIT 1;
UPDATE SERVICES SET SHOW_ONLINE='Y' WHERE SERVICEID='P12' LIMIT 1;
UPDATE SERVICES SET SHOW_ONLINE='Y' WHERE SERVICEID='C12' LIMIT 1;

?>