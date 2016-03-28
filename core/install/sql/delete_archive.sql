CREATE TABLE IF NOT EXISTS `{TBL_NAME}` (
  `delete_id` int(11) NOT NULL AUTO_INCREMENT,
  `source_table` varchar(50) NOT NULL,
  `data` blob NOT NULL,
  `delete_dt` datetime NOT NULL,
  PRIMARY KEY (`delete_id`),
  KEY `source_table` (`source_table`),
  KEY `delete_dt` (`delete_dt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;