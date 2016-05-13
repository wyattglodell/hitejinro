<?php
	if ($conf->dev_mode) {
		if ($_POST['action'] == 'install-setting') {
			$c = $sql->sanitize($_POST);
				
			$password = User::generate_password($c['admin_password']);
			
			$tables = glob($conf->core.'/install/sql/*.sql');
			
			foreach($tables as $v)
			{
				$query = file_get_contents($v);
				
				$name = strtoupper(str_replace('.sql', '', end(explode('/', $v))));
				
				if ($conf->{$name}) {
					$q = str_replace('{TBL_NAME}', $conf->{$name}, $query);
					
					$queries = explode('======', $q);
					
					foreach ($queries as $query)
					{
						$sql->query($query);
					}
				}
			}
			
			$sql->query("INSERT INTO `$conf->SITE_SETTING` (`name`, `value`, `group`, `type`, `options`, `info`) VALUES ('site_maintenance', 'Off', 'General', 'select', 'On|Off', '')");
			$sql->query("INSERT INTO `$conf->SITE_SETTING` (`name`, `value`, `group`, `type`, `options`, `info`) VALUES ('contact_name', '$c[contact_name]', 'General', 'text', '', 'Name that it will be sent as.')");
			$sql->query("INSERT INTO `$conf->SITE_SETTING` (`name`, `value`, `group`, `type`, `options`, `info`) VALUES ('contact_email', '$c[contact_email]', 'General', 'text', '', 'Email address that the contact form will be sent to.')");
			$sql->query("INSERT INTO `$conf->SITE_SETTING` (`name`, `value`, `group`, `type`, `options`, `info`) VALUES ('from_email', '$c[email_from]', 'General', 'text', '', 'The email address that will be displayed as being sent from - will also reply to this address.')");
			$sql->query("INSERT INTO `$conf->SITE_SETTING` (`name`, `value`, `group`, `type`, `options`, `info`) VALUES ('site_name', '$c[site_title]', 'General', 'text', '', 'Name of the website')");
			$sql->query("INSERT INTO `$conf->SITE_SETTING` (`name`, `value`, `group`, `type`, `options`, `info`) VALUES ('meta_tag', '$c[meta_tag]', 'General', 'text', '', 'Keywords describing this site, used for search engine purposes.')");
			$sql->query("INSERT INTO `$conf->SITE_SETTING` (`name`, `value`, `group`, `type`, `options`, `info`) VALUES ('meta_description', '$c[meta_description]', 'General', 'textarea', '', 'Paragraph describing the site, for search engine purposes')");
			$sql->query("INSERT INTO `$conf->SITE_SETTING` (`name`, `value`, `group`, `type`, `options`, `info`) VALUES ('home_url', 'home', 'System', 'text', '', 'Homepage URL')");
			$sql->query("INSERT INTO `$conf->SITE_SETTING` (`name`, `value`, `group`, `type`, `options`, `info`) VALUES ('load_time', 'On', 'System', 'select', 'On|Off', 'Show load time at the bottom of the page - only displays in Development Mode')");
			$sql->query("INSERT INTO `$conf->SITE_SETTING` (`name`, `value`, `group`, `type`, `options`, `info`) VALUES ('admin_home', '', 'System', 'text', '', 'Admin Homepage')");
			
			$sql->query("INSERT INTO `$conf->SITE_SETTING` (`name`, `value`, `group`, `type`, `options`, `info`) VALUES ('cache_expiration', '86400', 'System', 'text', '', 'Number of seconds for cached elements to expire.')");
			$sql->query("INSERT INTO `$conf->SITE_SETTING` (`name`, `value`, `group`, `type`, `options`, `info`) VALUES ('css_caching', 'Off', 'System', 'select', 'On|Off', 'Cache CSS and JS files.')");
			$sql->query("INSERT INTO `$conf->SITE_SETTING` (`name`, `value`, `group`, `type`, `options`, `info`) VALUES ('pages_to_cache', '', 'System', 'textarea', '', 'Enter pages to cache. One per line\n\nExamples: \ncontact/form or contact/* or contact*')");
			
			$sql->query("INSERT INTO `$conf->USER` (`user_id`, `username`, `password`, `email`, `first_name`, `last_name`, `phone`, `status`, `temp_password`, `create_dt`) VALUES (2, '$c[admin_username]', '$password', '$c[contact_email]','','','', 1, '', NOW())");
			$sql->query("INSERT INTO `$conf->USER_ROLE` (`user_id`, `role_id`) VALUES (2, 1)");

			$user = new User;
			$status = $user->login(array('username'=>$c['admin_username'],'password'=>$c['admin_password']));

			if ($status) {
				$func->redirect($conf->admin, 'Installation completed. All files are currently set to 0777, set it lower if it can be.');
			}
		}

		$tpl->set_template('header', '');
		$tpl->set_template('footer', '');
		$tpl->set_template('content', 'install-setting.tpl.php');
	} else {
		$func->redirect($conf->http, '');	
	}
?>