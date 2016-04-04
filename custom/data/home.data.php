<?php
	$site = Site::get_current_site();
	$sites = Site::$sites;

	if ( isset($_GET['site']) && in_array($_GET['site'], $sites) ) {
		$site = $_GET['site'];
		Site::set_current_site($site);
	}

	if (!$site)  $func->redirect('/entry');

	if ($func->load_data($site.'/home', true)) {
		include ($func->load_data($site.'/home', true));
	}
?>