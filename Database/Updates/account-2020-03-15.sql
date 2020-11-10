ALTER TABLE `account` ADD `exported` tinyint(1) NOT NULL DEFAULT '0' AFTER `deleted`;
ALTER TABLE `account` ADD `party` varchar(255) NOT NULL AFTER `exported`;
ALTER TABLE `account` ADD `resigned_timestamp` int(11) NOT NULL DEFAULT '0' AFTER `party`;

ALTER TABLE `account` ADD INDEX(`party`);
ALTER TABLE `account` ADD INDEX(`resigned_timestamp`);
