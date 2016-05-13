<?php
	$tpl->assign('states', $func->get_list('states'));

	if ($_POST['action'] == 'register') {		
		$clean = $sql->sanitize($_POST);
		$user = new User;
		
		$msg = $user->register($clean);
		$func->set_msg($msg);
		$tpl->assign('form', $clean);
	}
	
	$tpl->set_template('content', 'register.tpl.php');
?>