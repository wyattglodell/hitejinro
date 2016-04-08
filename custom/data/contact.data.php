<?php
	$site = Site::get_current_site();
	
	$tpl->body_classes($site);
	$tpl->set_template('content', 'contact.tpl.php');
?>