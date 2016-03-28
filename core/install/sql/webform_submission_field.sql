CREATE TABLE IF NOT EXISTS `{TBL_NAME}` (
	`submission_id` int(11) NOT NULL,
	`name` varchar(100) NOT NULL,
	`value` text NOT NULL,
	KEY `submission_id` (`submission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;