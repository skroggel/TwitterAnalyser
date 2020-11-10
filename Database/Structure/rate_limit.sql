CREATE TABLE IF NOT EXISTS `rate_limit` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `create_timestamp` int(11) NOT NULL DEFAULT '0',
  `change_timestamp` int(11) NOT NULL DEFAULT '0',

  `category` varchar(255) DEFAULT NULL,
  `method` varchar(255) DEFAULT NULL,
  `limits` int(11) NOT NULL DEFAULT '0',
  `remaining` int(11) NOT NULL DEFAULT '0',
  `reset` int(11) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',

  PRIMARY KEY (uid),
  UNIQUE KEY `category_method` (`category`,`method`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;