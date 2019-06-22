CREATE TABLE IF NOT EXISTS `tweet` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `create_timestamp` int(11) NOT NULL DEFAULT '0',
  `change_timestamp` int(11) NOT NULL DEFAULT '0',

  `tweet_id` bigint(20) NOT NULL DEFAULT '0',
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `user_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `account` int(11) NOT NULL DEFAULT '0',
  `type` varchar(255) NOT NULL,
  `created_at` int(11) NOT NULL DEFAULT '0',
  `full_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hashtags` text,
  `mentions` text,
  `urls` text,
  `symbols` text,
  `media` text,
  `source` varchar(255) NOT NULL,
  `in_reply_to_tweet_id` bigint(20) NOT NULL DEFAULT '0',
  `in_reply_to_user_id` bigint(20) NOT NULL DEFAULT '0',
  `retweet_count` int(11) NOT NULL DEFAULT '0',
  `favorite_count` int(11) NOT NULL DEFAULT '0',

  PRIMARY KEY (uid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
