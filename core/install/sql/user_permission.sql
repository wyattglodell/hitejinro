CREATE TABLE IF NOT EXISTS `{TBL_NAME}` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `action` varchar(255) NOT NULL,
  `group` varchar(75) NOT NULL,
  PRIMARY KEY (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

======


INSERT INTO `{TBL_NAME}` (`permission_id`, `action`, `group`) VALUES
(1, 'Access Administrator Panel', 'Administrator'),
(15, 'Access Log', 'Administrator'),
(6, 'Access Users Permissions', 'Administrator'),
(8, 'Access Pages', 'Administrator'),
(9, 'Access Users', 'Administrator'),
(10, 'Access Webform', 'Administrator'),
(11, 'Manage Webforms', 'Webforms'),
(12, 'Access Webform Fields', 'Administrator'),
(13, 'Access Webform Generate', 'Administrator'),
(14, 'Access User Roles', 'Administrator'),
(16, 'Access Menu', 'Administrator'),
(18, 'Access Settings', 'Administrator'),
(20, 'Manage System Settings', 'Settings'),
(21, 'Access File Manager', 'File Manager'),
(22, 'Upload Files', 'File Manager');