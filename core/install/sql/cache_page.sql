CREATE TABLE IF NOT EXISTS `{TBL_NAME}` (
  `request` varchar(255) NOT NULL,
  `host` varchar(255) NOT NULL,
  `data` longtext NOT NULL,
  `expire` datetime NOT NULL,
  UNIQUE KEY `request` (`request`,`host`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;