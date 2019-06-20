CREATE TABLE IF NOT EXISTS `accounts` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `create_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `change_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

  `account_id` varchar(255) NOT NULL,
  PRIMARY KEY (uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `log` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `create_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `change_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

  `level` tinyint NOT NULL DEFAULT '0',
  `method` varchar(255) NOT NULL,
  `api_call` varchar(255) NOT NULL,
  `comment` varchar(255) NOT NULL,
  PRIMARY KEY (uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `rate_limits` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `create_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `change_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,

  `type` varchar(255) NOT NULL,
  `method` varchar(255) NOT NULL,
  `limits` int(11) NOT NULL,
  `remaining` int(11) NOT NULL,
  `reset` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;