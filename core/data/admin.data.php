<?php
	$admin = new Admin;
	Registry::set($admin);

	$admin->set_manager($get->b);
	
	$tpl->set_template('content', 'admin/admin.tpl.php');
	$tpl->set_template('menu', 'admin/menu.tpl.php');
	$tpl->set_template('header', 'admin/header.tpl.php');
	$tpl->set_template('footer', 'admin/footer.tpl.php');
	
	$tpl->jquery_ui();
	$tpl->fancybox();
	$tpl->fileupload();
	
	$tpl->base_css('icon.css');  
	$tpl->base_css('admin.css',true);  
	

	$tpl->js($conf->editor);
	$tpl->base_js('tablesort.js');
	$tpl->base_js('admin.js', true);
	
	$tpl->assign('admin_url', $conf->admin);	
	
	$tpl->assign('manager', $admin->get_manager()); 
	$tpl->assign('filemanager_url', $conf->filemanager);
	$tpl->assign('filemanager_upload_url', $conf->filemanager_upload);

	$tpl->assign('file_path', $conf->path);
	$tpl->assign('file_upload', $conf->upload);

	$tpl->assign('user', $user->get_user());
	$tpl->assign('real_user', $_SESSION['real_user']);
	
	$tpl->head_inc("<link href='//fonts.googleapis.com/css?family=Lato:300,400,700|Open+Sans+Condensed:700' rel='stylesheet' type='text/css'>");
	
	$tpl->js_var('logout_url', $conf->admin.'/logout');

	$tpl->assign('profile_url', $conf->admin.'/administrator:users&amp;action=edit&amp;id='.$user->get_user_id().'&amp;destination='.$func->base64_encode($conf->current_uri));
	
	if ($get->b) {
		$name = end(explode(':', $get->b));
		$tpl->assign('metatitle', $admin->clean_admin_name($name).' | '.$setting->site_name, true);
	}
	
	if ($get->b == 'logout') {
		$func->load_data('admin/logout');
	} else if ($get->b == 'reset-password' && $get->c) {
		$func->load_data('admin/reset-password');
	} else if (!user_access('Access Administrator Panel', 'Administrator')) {
		$tpl->body_classes('login');
		
		if ($get->ajax) {
			$func->set_msg("Your session has expired. Please-login again.");
			
			echo json_encode(array('login'=>true)); die;
		} else {
			$func->load_data('admin/login');
		}
	} else {		
		if ($get->switch_back && $_SESSION['real_user']) {
			$user->set_user($_SESSION['real_user']);
			unset($_SESSION['real_user']);
			
			$func->log("User account switched back to ".$user->get_username());
			$func->reload("User account switched back to ".$user->get_username());	
		}
		
		
		$tpl->set_template('admin', 'admin/default.tpl.php');
		$tpl->set_template('admin_filter', 'admin/admin_filter.tpl.php');
		
		if (!$get->b && $setting->admin_home) {
			$func->redirect($conf->admin.'/'.$setting->admin_home);	
		}

		$tpl->assign('admin_menu', $admin->admin_manager_menu());


		if ($get->b) {
			if ($user->logged_in()) {
				$admin->set_page($conf->current_url);
				$admin->set_param();
				$admin->paginate(75);
				$admin->search_filter();
				$admin->load_tpl($tpl);
				
				$admin->set_update_tpl('admin/form.tpl.php', 'admin');
				
				# has access or editing self profile
				if (user_access('Access '.$admin->clean_admin_name($get->b), 'Administrator') || ($admin->clean_admin_name($get->b) == 'Users' && ($admin->action == 'edit' || $admin->action == 'submit_edit') && $admin->id == $user->get_user_id())) {
					
					$found = $func->load_data('admin/'.str_replace(':','/',$get->b));
					
					if ($found === false) {
						$func->log('Requested page was not found', '', 'warning');
						$func->redirect($conf->admin, 'You do not have the permissions to access this page');
					} else {
						$admin->default_control();
						
						$admin->run();
					}
				} else {
					$func->log('Invalid admin page requested', '', 'warning');
					$func->redirect($conf->admin, 'You do not have the permissions to access this page');
				}
			} else {
				$func->log('Session IP or UA does not match', array('session'=>$_SESSION, 'server'=>$_SERVER), 'notice');
				$func->redirect($conf->admin.'/logout', 'Looks like your session has expired, please log-in again');
			}
		} else {
			$admin->load_tpl($tpl);
			$admin->set_keyword('Dashboard');
			
			$tpl->assign('admin_menu_raw', $admin->admin_menu_raw);
			$tpl->set_template('admin', 'admin/dashboard.tpl.php');
			
			$admin->run();
		}
	}
?>