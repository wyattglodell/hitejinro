CREATE TABLE IF NOT EXISTS `{TBL_NAME}` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  UNIQUE KEY `role_id` (`role_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

======

INSERT INTO `{TBL_NAME}` (`role_id`, `permission_id`) VALUES
(2, 1),
(2, 6),
(2, 8),
(2, 9),
(2, 10),
(2, 14),
(2, 16),
(2, 18),
(2, 21),
(2, 22);