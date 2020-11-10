ALTER TABLE `account` ADD `is_secondary` TINYINT(1) NOT NULL DEFAULT '0' AFTER `suggestion_for_name`;
