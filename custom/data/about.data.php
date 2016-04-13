<?php
	$site = Site::get_current_site();
	$tpl->set_template('content', $site.'/about.tpl.php');
?>