CREATE TABLE IF NOT EXISTS `{TBL_NAME}` (
  `cache_token` varchar(255) NOT NULL,
  `host` varchar(255) NOT NULL,
  `data` longblob NOT NULL,
  `created` int(11) NOT NULL,
  UNIQUE KEY `cache_token` (`cache_token`,`host`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;