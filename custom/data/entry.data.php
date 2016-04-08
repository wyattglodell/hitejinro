<?php
	if ($_POST['action'] === 'verify') {
		$verified = false;
		$msg = 'Sorry, you must be of legal drinking age to access this site.';
		
		Site::set_age($_POST['age']);
		$verified = Site::age_verified();
		
		if ($verified) {
			$func->redirect($conf->https.'/home');	
		} else {
			$func->reload($msg);	
		}
	}

	$tpl->js('facebook_api.js', '', true);
	$tpl->js('entry.js', '', true);
	
	$tpl->set_template('body', 'entry.tpl.php');
?>