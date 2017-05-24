use MOBILE_API;

INSERT INTO `BROWSER_NOTIFICATION_TEMPLATE` (`ID`, `NOTIFICATION_KEY`, `MESSAGE`, `TITLE`, `ICON`, `TAG`, `CHANNEL`, `STATUS`, `SUBSCRIPTION`, `FREQUENCY`, `TIME_CRITERIA`, `LANDING_ID`, `TTL`, `COUNT`) VALUES (1, 'JUST_JOIN', '{MATCH_COUNT} new matching profiles have joined Jeevansathi since you last checked	', 'Just Joined Matches	', 'D', 'JJ', 'M,D', '', 'A', 'D', '', 1, 0, 'SINGLE');
INSERT INTO `BROWSER_NOTIFICATION_TEMPLATE` (`ID`, `NOTIFICATION_KEY`, `MESSAGE`, `TITLE`, `ICON`, `TAG`, `CHANNEL`, `STATUS`, `SUBSCRIPTION`, `FREQUENCY`, `TIME_CRITERIA`, `LANDING_ID`, `TTL`, `COUNT`) VALUES (2, 'JUST_JOIN', '{MATCH_COUNT} new matching profiles have joined Jeevansathi since you last checked	', 'Just Joined Matches	', 'D', 'JJ', 'M,D', '', 'A', 'D', '', 1, 0, 'MUL');
INSERT INTO `BROWSER_NOTIFICATION_TEMPLATE` (`ID`, `NOTIFICATION_KEY`, `MESSAGE`, `TITLE`, `ICON`, `TAG`, `CHANNEL`, `STATUS`, `SUBSCRIPTION`, `FREQUENCY`, `TIME_CRITERIA`, `LANDING_ID`, `TTL`, `COUNT`) VALUES (3, 'AGENT_ONLINE_PROFILE', '{USERNAME_OTHER_1} is now online', 'My online profiles', 'D', NULL, 'CRM_AND', '', 'A', 'D', '', 2, 0, 'SINGLE');
INSERT INTO `BROWSER_NOTIFICATION_TEMPLATE` (`ID`, `NOTIFICATION_KEY`, `MESSAGE`, `TITLE`, `ICON`, `TAG`, `CHANNEL`, `STATUS`, `SUBSCRIPTION`, `FREQUENCY`, `TIME_CRITERIA`, `LANDING_ID`, `TTL`, `COUNT`) VALUES (4, 'AGENT_FP_PROFILE', '{USERNAME_OTHER_1} has made a failed payment try', 'My FP profiles', 'D', NULL, 'CRM_AND', '', 'A', 'D', '', 2, 0, 'SINGLE');       