CREATE TABLE IF NOT EXISTS `{TBL_NAME}` (
	`setting_id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(50) NOT NULL,
	`value` text NOT NULL,
	`group` varchar(50) NOT NULL,
	`type` varchar(25) NOT NULL,
	`options` text NOT NULL,
	`info` text NOT NULL,
	PRIMARY KEY (`setting_id`),
	UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;