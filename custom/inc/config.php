<?php
	$conf->set('installed', true);
	$conf->set('fastboot', array('async'));	# these $get->a requests don't go through the bootstrap or the template
	
	$conf->set('db_timezone', 'PST'); # if DB is in a different timezone, using NOW() will result in a timestamp mismatch due to date_default_timezone_set('America/Los_Angeles');

	$conf->set('master_username', 'excela');
	$conf->set('master_password', '$2a$10$b69565c67c28d5eff95025$|$2a$10$b69565c67c28d5eff9502uch9l1ZNxTjbPbQVS0uTvME0BpOZMovK');
	$conf->set('dev_email', 'kenny@excelacreative.com');
	$conf->set('dev_mode', true);
	$conf->set('db_prefix', 'hitejinro_');
	$conf->set('db_host', 'localhost');
	$conf->set('db_name', 'hjusa_2016');
	$conf->set('db_username', 'hjusa_2016');
	$conf->set('db_password', 'T2trkWCdQ4ei');
	
	$conf->set('sites', array('hite'=>'Hite','jinro'=>'Jinro'));
	
	$conf->set('NEWS',$conf->db_prefix.'news');
	$conf->set('FAN_PAGE',$conf->db_prefix.'fan_page');
	$conf->set('RECIPE',$conf->db_prefix.'recipe');
	$conf->set('PRODUCT',$conf->db_prefix.'product');
	$conf->set('STORE_LOCATOR',$conf->db_prefix.'store_locator');
?>