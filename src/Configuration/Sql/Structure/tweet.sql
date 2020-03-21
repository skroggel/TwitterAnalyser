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
  `full_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hashtags` text NOT NULL,
  `hashtags_words_only` text NOT NULL,
  `mentions` text NOT NULL,
  `urls` text NOT NULL,
  `symbols` text NOT NULL,
  `media` text NOT NULL,
  `source` varchar(255) NOT NULL,
  `is_reply` tinyint(1) NOT NULL DEFAULT '0',
  `in_reply_to_tweet_id` bigint(20) NOT NULL DEFAULT '0',
  `in_reply_to_user_id` bigint(20) NOT NULL DEFAULT '0',
  `retweet_count` int(11) NOT NULL DEFAULT '0',
  `favorite_count` int(11) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `exported` tinyint(1) NOT NULL DEFAULT '0',
  `interaction_time` int(11) NOT NULL DEFAULT '0',
  `reply_count` int(11) NOT NULL DEFAULT '0',
  `calculation_timestamp` int(11) NOT NULL DEFAULT '0',

  PRIMARY KEY (uid),
  KEY `account_type` (`account`,`type`),
  KEY `tweet_id_type` (`tweet_id`,`type`),
  KEY `created_at` (`created_at`),
  KEY `in_reply_type` (`in_reply_to_tweet_id`, `type`),
  KEY `user_id` (`user_id`),
  KEY `user_name` (`user_name`),
  KEY `interaction_time` (`interaction_time`),
  KEY `reply_count` (`reply_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `tweet` ADD FULLTEXT(`hashtags_words_only`);

