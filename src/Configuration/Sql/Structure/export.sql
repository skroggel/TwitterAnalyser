CREATE TABLE IF NOT EXISTS `export` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `create_timestamp` int(11) NOT NULL DEFAULT '0',
  `change_timestamp` int(11) NOT NULL DEFAULT '0',

  `md5_user_id` varchar(255) NOT NULL,
  `md5_user_name` varchar(255) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',

  PRIMARY KEY (uid),
  KEY `md5_user_id` (`md5_user_id`),
  KEY `md5_user_name` (`md5_user_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
