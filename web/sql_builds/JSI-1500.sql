use MAIL;
CREATE TABLE `contactViewersMail` (
  `RECEIVER` int(11) NOT NULL,
  `DATE` datetime NOT NULL,
  PRIMARY KEY (`RECEIVER`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;