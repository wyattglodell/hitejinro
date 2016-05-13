CREATE TABLE IF NOT EXISTS `{TBL_NAME}` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `level`int(11) NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

======

INSERT INTO `{TBL_NAME}` (`role_id`, `name`, `level`) VALUES
(1, 'Super Administrator', 0),
(2, 'Administrator', 10);