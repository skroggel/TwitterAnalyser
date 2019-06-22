CREATE TABLE IF NOT EXISTS `account` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `create_timestamp` int(11) NOT NULL DEFAULT '0',
  `change_timestamp` int(11) NOT NULL DEFAULT '0',

  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `user_name` varchar(255) NOT NULL,

  `fetch_timeline_timestamp` int(11) NOT NULL DEFAULT '0',
  `fetch_addressed_timestamp` int(11) NOT NULL DEFAULT '0',

  PRIMARY KEY (uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

