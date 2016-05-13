CREATE TABLE IF NOT EXISTS `{TBL_NAME}` (
  `session_id` varchar(32) NOT NULL,
  `access` int(11) unsigned DEFAULT NULL,
  `data` text,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;