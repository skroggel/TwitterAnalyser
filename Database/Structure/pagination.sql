CREATE TABLE IF NOT EXISTS `pagination` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `create_timestamp` int(11) NOT NULL DEFAULT '0',
  `change_timestamp` int(11) NOT NULL DEFAULT '0',

  `account` int(11) NOT NULL DEFAULT '0',
  `type` varchar(255) NOT NULL,
  `highest_id` bigint(20) NOT NULL DEFAULT '0',
  `lowest_id` bigint(20) NOT NULL DEFAULT '0',
  `last_lowest_id` bigint(20) NOT NULL DEFAULT '0',
  `since_id` bigint(20) NOT NULL DEFAULT '0',
  `max_id` bigint(20) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',

  PRIMARY KEY (uid),
  UNIQUE KEY `account_type` (`account`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

