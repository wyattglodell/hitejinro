<?php
	if ($get->b) {
		$token = $sql->sanitize($get->b);

		$sql->query("SELECT * FROM $conf->USER WHERE temp_token = '$token'");
		$row = $sql->fetch();
		
		if (!$row['user_id']) {
			$func->log("Invalid password reset link");
			$func->redirect($conf->https, 'Invalid password reset link');
		}
	}

	if ($_POST['action'] == 'reset' && $row) {
		if (!empty($_POST['form_id']) && $_SESSION['form_id'] == $_POST['form_id']) {
			$user = new User;
			$user->reset_password($_POST, $row, $conf->https);
		} else {
			$func->log("Invalid Form ID");
			$func->reload("Invalid submission, please make sure your session cookie is enabled and re-submit the form");
		}
	}	
	
	$_SESSION['form_id'] = md5(rand().time());

	$tpl->assign('form_id', $_SESSION['form_id']);
	
	$tpl->set_template('body', 'reset-password.tpl.php');
?>