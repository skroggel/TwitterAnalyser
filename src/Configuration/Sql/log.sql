CREATE TABLE IF NOT EXISTS `log` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
`create_timestamp` int(11) NOT NULL DEFAULT '0',
`change_timestamp` int(11) NOT NULL DEFAULT '0',

  `level` tinyint NOT NULL DEFAULT '0',
  `class` varchar(255) NOT NULL,
  `method` varchar(255) NOT NULL,
  `api_call` varchar(255) NOT NULL,
  `comment` varchar(255) NOT NULL,
  PRIMARY KEY (uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
