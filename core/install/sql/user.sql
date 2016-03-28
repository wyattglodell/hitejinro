CREATE TABLE IF NOT EXISTS `{TBL_NAME}` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `temp_password` varchar(255) NOT NULL,
  `temp_token` varchar(32) NOT NULL,
  `create_dt` datetime NOT NULL,
  `last_login_dt` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;