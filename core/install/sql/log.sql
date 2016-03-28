CREATE TABLE IF NOT EXISTS `{TBL_NAME}` (
	`log_id` INT NOT NULL AUTO_INCREMENT ,
	`message` VARCHAR( 250 ) NOT NULL ,
	`ip` VARCHAR( 23 ) NOT NULL ,
    `uri` text NOT NULL,
    `username` varchar(50) NOT NULL,	
	`time` DATETIME NOT NULL ,
	`data` BLOB NOT NULL ,
	`severity` VARCHAR(15) NOT NULL ,
	PRIMARY KEY ( `log_id` )
) ENGINE=InnoDB DEFAULT CHARSET=utf8;