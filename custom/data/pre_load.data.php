<?php
	$anonymous = array ('privacy-policy','terms-and-conditions', 'home','entry');

	if ($get->a != 'entry' && !Site::age_verified()) {
		$func->redirect($conf->https.'/entry');
	}

	if ( isset($get->site) && $conf->sites[$get->site] ) {
		Site::set_current_site($get->site);
	}
	
	$site = Site::get_current_site();

	if (!in_array($get->a, $anonymous) && !$site) {
		
		$func->redirect($conf->https.'/home');
	}


	if ($_POST['action'] == 'submit-mailinglist') {
		$webform = new Webform('mailinglist');
		$webform->load();
		
		$result = $webform->submit($_POST);
		$func->reload($result['msg']);
	}




	$tpl->css('normalize.css', true);
	$tpl->css('main.css', true);
	$tpl->js('custom.js', '', true);

	$tpl->assign('favicon', $conf->public.'/img/favicon.png', true);
	
	$site_data = Site::get_site_data($site);
	
	if (!$site) {
		$tpl->body_classes('split-choice');
		$tpl->set_template('body', 'home.tpl.php');		
	} else {
		$tpl->body_classes($site);
		$tpl->assign('current_site', $site);
		
		$tpl->assign('sites', Site::get_sites());
		$tpl->assign('site', $site);
		
		$tpl->assign('social_links', $site_data['social']);
		$tpl->assign('navigation',$site_data['pages']);
		$tpl->assign('footer_navigation',$site_data['footer_navigation']);
	}
?>