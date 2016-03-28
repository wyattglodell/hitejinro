<?php
	if ($_POST['action'] == 'login') {
		$user = new User;
		$user->login($_POST, $conf->current_uri);

		if ($user->is_atleast('admin')) {
			$func->reload();
		} 
	}	
	
	$tpl->assign('login_destination', $get->b == 'logout' ? $conf->admin : $conf->current_uri);
		
	$tpl->set_template('admin', 'admin/login.tpl.php');
?>