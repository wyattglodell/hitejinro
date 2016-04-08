<?php
	$site = Site::get_current_site();
	$sites = Site::$sites;

	if ( isset($_GET['site']) && in_array($_GET['site'], $sites) ) {
		$site = $_GET['site'];
		Site::set_current_site($site);
	}

	if ($site) {
		r($site_data);
		$tpl->assign('site', $site);
		$tpl->body_classes($site);
		$tpl->set_template('content', 'home.tpl.php');
	} else {
		$tpl->body_classes('split-choice');
		$tpl->set_template('body', 'home.tpl.php');
	}

?>