use MOBILE_API;

ALTER TABLE MOBILE_API.BROWSER_NOTIFICATION_TEMPLATE ADD DAILY_LIMIT int(5) DEFAULT 0;

INSERT INTO MOBILE_API.BROWSER_NOTIFICATION_TEMPLATE (`ID`, `NOTIFICATION_KEY`, `MESSAGE`, `TITLE`, `ICON`, `TAG`, `CHANNEL`, `STATUS`, `SUBSCRIPTION`, `FREQUENCY`, `TIME_CRITERIA`, `LANDING_ID`, `TTL`, `COUNT`, `DAILY_LIMIT`) VALUES (NULL, 'EOI', '{USERNAME_OTHER_1} ({AGE_OTHER_1} yrs, {CASTE_OTHER_1}) from {CITY_RES_OTHER_1} has expressed interest in you', 'Interest Received', 'P', 'IR', 'M', 'Y', 'A', 'D', '', 6, 86400, 'SINGLE',3);
INSERT INTO MOBILE_API.BROWSER_NOTIFICATION_TEMPLATE (`ID`, `NOTIFICATION_KEY`, `MESSAGE`, `TITLE`, `ICON`, `TAG`, `CHANNEL`, `STATUS`, `SUBSCRIPTION`, `FREQUENCY`, `TIME_CRITERIA`, `LANDING_ID`, `TTL`, `COUNT`, `DAILY_LIMIT`) VALUES (NULL, 'MESSAGE_RECEIVED', '{USERNAME_OTHER_1} sent you a message {MESSAGE_RECEIVED}', 'New Message Received', 'P', 'MR', 'M', 'Y', 'A', 'D', '', 7, 86400, 'SINGLE',0);
INSERT INTO MOBILE_API.BROWSER_NOTIFICATION_TEMPLATE (`ID`, `NOTIFICATION_KEY`, `MESSAGE`, `TITLE`, `ICON`, `TAG`, `CHANNEL`, `STATUS`, `SUBSCRIPTION`, `FREQUENCY`, `TIME_CRITERIA`, `LANDING_ID`, `TTL`, `COUNT`, `DAILY_LIMIT`) VALUES (NULL, 'EOI_REMINDER', '{MESSAGE_RECEIVED}', 'Interest Reminder Received', 'P', 'ERR', 'M', 'Y', 'A', 'D', '', 6, 86400, 'SINGLE',3);