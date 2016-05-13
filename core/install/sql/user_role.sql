CREATE TABLE IF NOT EXISTS `{TBL_NAME}` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `weight` int(11) NOT NULL,
  KEY `user_id` (`user_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;