CREATE TABLE IF NOT EXISTS `{TBL_NAME}` (
	`page_id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`url` varchar(255) NOT NULL,
	`weight` int(11) NOT NULL,
	`menu` tinyint(4) NOT NULL,
	`left_id` int(11) NOT NULL,
	`right_id` int(11) NOT NULL,
	`parent_id` int(11) NOT NULL,
	`is_content` tinyint(4) NOT NULL,
	`attachable` tinyint(4) NOT NULL,
	`alias` varchar(255) NOT NULL,
	`full_alias` varchar(255) NOT NULL,
	`content` text NOT NULL,
	`title` varchar(255) NOT NULL,
	`menu_title` varchar(255) NOT NULL,
	`keyword` text NOT NULL,
	`description` text NOT NULL,
	`status` tinyint(4) NOT NULL,
	`modified` datetime NOT NULL,
	`author` varchar(100) NOT NULL,
	PRIMARY KEY (`page_id`),
	UNIQUE KEY `alias` (`alias`),
	KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;