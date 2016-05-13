CREATE TABLE IF NOT EXISTS `{TBL_NAME}` (
	`submission_id` int(11) NOT NULL AUTO_INCREMENT,
	`webform_id` int(11) NOT NULL,
	`submission_date` datetime NOT NULL,
	`ip` varchar(39) NOT NULL,
	`downloaded` tinyint(4) NOT NULL,
	PRIMARY KEY (`submission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;