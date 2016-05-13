CREATE TABLE IF NOT EXISTS `{TBL_NAME}` (
	`webform_id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL,
	`email` text NOT NULL,
	`captcha` tinyint(4) NOT NULL,
	`slug` varchar(100) NOT NULL,
	`subject` varchar(100) NOT NULL,
	`from_name` varchar(100) NOT NULL,
	`from_email` varchar(125) NOT NULL,
	PRIMARY KEY (`webform_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

======

INSERT INTO `{TBL_NAME}` (`webform_id`, `name`, `email`, `captcha`, `slug`, `subject`, `from_name`, `from_email`) VALUES
(1, 'contact', 'default', 1, 'contact', 'Webform submission from contact form', 'default', 'default');			
