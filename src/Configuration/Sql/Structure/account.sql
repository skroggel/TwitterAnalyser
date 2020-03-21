CREATE TABLE IF NOT EXISTS `account` (
  `uid` int(11) NOT NULL,
  `create_timestamp` int(11) NOT NULL DEFAULT '0',
  `change_timestamp` int(11) NOT NULL DEFAULT '0',

  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `user_name` varchar(255) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `url` varchar(255) DEFAULT NULL,
  `created_at` int(11) NOT NULL DEFAULT '0',
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `is_suggestion` tinyint(1) NOT NULL DEFAULT '0',
  `suggestion_for_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_secondary` tinyint(1) NOT NULL DEFAULT '0',
  `followers_count` int(11) NOT NULL DEFAULT '0',
  `friends_count` int(11) NOT NULL DEFAULT '0',
  `listed_count` int(11) NOT NULL DEFAULT '0',
  `favorites_count` int(11) NOT NULL DEFAULT '0',
  `statuses_count` int(11) NOT NULL DEFAULT '0',

  `party` varchar(255) NOT NULL,
  `resigned_timestamp` int(11) NOT NULL DEFAULT '0'
  `fetch_timeline_timestamp` int(11) NOT NULL DEFAULT '0',
  `fetch_addressed_timestamp` int(11) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `exported` tinyint(1) NOT NULL DEFAULT '0',

  ADD PRIMARY KEY (`uid`),
  ADD KEY `fetch_timeline_timestamp` (`fetch_timeline_timestamp`),
  ADD KEY `fetch_addressed_timestamp` (`fetch_addressed_timestamp`),
  ADD KEY `created_at` (`created_at`),
  ADD KEY `party` (`party`),
  ADD KEY `resigned_timestamp` (`resigned_timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

