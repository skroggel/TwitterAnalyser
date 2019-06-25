CREATE TABLE IF NOT EXISTS `url` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `create_timestamp` int(11) NOT NULL DEFAULT '0',
  `change_timestamp` int(11) NOT NULL DEFAULT '0',

  `url` varchar(255) NOT NULL,
  `base_url` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `processed` tinyint(1) NOT NULL DEFAULT '0',

  PRIMARY KEY (uid),
  KEY `processed` (`processed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

