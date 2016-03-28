CREATE TABLE IF NOT EXISTS `{TBL_NAME}` (
	`field_id` int(11) NOT NULL AUTO_INCREMENT,
	`webform_id` int(11) NOT NULL,
	`label` varchar(100) NOT NULL,
	`name` varchar(100) NOT NULL,
	`type` varchar(25) NOT NULL,
	`subtype` varchar(25) NOT NULL,
	`options` text NOT NULL,
	`required` tinyint(4) NOT NULL,
	`unique` tinyint(4) NOT NULL,
	`weight` int(11) NOT NULL,
	PRIMARY KEY (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


======

INSERT INTO `{TBL_NAME}` (`field_id`, `webform_id`, `label`, `name`, `type`, `subtype`, `options`, `required`, `weight`) VALUES
(2, 1, 'First Name', 'first_name', 'text', '', '', 1, 10),
(3, 1, 'Last Name', 'last_name', 'text', '', '', 1, 20),
(4, 1, 'Email', 'email', 'text', 'email', '', 1, 30),
(5, 1, 'Address', 'address', 'text', '', '', 0, 40),
(6, 1, 'City', 'city', 'text', '', '', 0, 50),
(12, 1, 'State', 'state', 'select', 'states', '', 0, 60),
(13, 1, 'Zip', 'zip', 'text', '', '', 0, 70),
(14, 1, 'Comment', 'comment', 'textarea', '', '', 1, 80);
