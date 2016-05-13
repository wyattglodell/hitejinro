<?php
	if ($_POST['action'] == 'reset') {
		$user = new User;
		$user->forgot_password($_POST, $conf->https);
	}	
	
	$tpl->body_classes('home');
	
	$tpl->set_template('body', 'forgot-password.tpl.php');
?>