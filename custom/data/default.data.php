<?php
	$site = Site::get_current_site();

	if (!$site) $func->redirect('/entry');
	
	if ($func->load_data($site.'/'.$get->a, true)) {
		include ($func->load_data($site.'/'.$get->a, true));
	}
?>