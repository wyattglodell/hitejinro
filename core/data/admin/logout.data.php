<?php	
	$user = new User;
	$user->logout();
	$func->redirect($conf->admin, "You have been logged out successfully");
?>