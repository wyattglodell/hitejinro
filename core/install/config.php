<?php
	$conf->set('installed', true);
	$conf->set('fastboot', array('async'));	# these $get->a requests don't go through the bootstrap or the template
	
	$conf->set('db_timezone', 'PST'); # if DB is in a different timezone, using NOW() will result in a timestamp mismatch due to date_default_timezone_set('America/Los_Angeles');

	$conf->set('master_username', '{master_username}');
	$conf->set('master_password', '{master_password}');
	$conf->set('dev_email', '{dev_email}');
	$conf->set('dev_mode', true);
	$conf->set('db_prefix', '{prefix}');
	$conf->set('db_host', '{db_host}');
	$conf->set('db_name', '{db_name}');
	$conf->set('db_username', '{db_username}');
	$conf->set('db_password', '{db_password}');
?>