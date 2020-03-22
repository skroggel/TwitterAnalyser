ALTER TABLE `tweet` ADD `exported` tinyint(1) NOT NULL DEFAULT '0' AFTER `deleted`;
ALTER TABLE `tweet` ADD `hashtags_words_only` text NOT NULL AFTER `hashtags`;
ALTER TABLE `tweet` ADD `interaction_time` INT(11) NOT NULL DEFAULT '0' AFTER `exported`;
ALTER TABLE `tweet` ADD `reply_count` INT(11) NOT NULL DEFAULT '0' AFTER `interaction_time`;
ALTER TABLE `tweet` ADD `calculation_timestamp` INT(11) NOT NULL DEFAULT '0' AFTER `reply_count`;

ALTER TABLE `tweet` ADD INDEX(`user_id`);
ALTER TABLE `tweet` ADD INDEX(`user_name`);
ALTER TABLE `tweet` ADD INDEX(`type`);
ALTER TABLE `tweet` ADD INDEX(`is_reply`);
ALTER TABLE `tweet` ADD INDEX(`exported`);
ALTER TABLE `tweet` ADD INDEX(`interaction_time`);
ALTER TABLE `tweet` ADD INDEX(`calculation_timestamp`);
ALTER TABLE `tweet` ADD INDEX(`reply_count`);
ALTER TABLE `tweet` ADD FULLTEXT(`hashtags_words_only`);



