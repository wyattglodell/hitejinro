<?php
	$site = Site::get_current_site();
	$public_pages = array('privacy-policy','terms-and-conditions');

	if (!$site && !in_array($get->a, $public_pages)) $func->redirect('/entry');
	
	if ($func->load_data($site.'/'.$get->a, true)) {
		$func->load_data($site.'/'.$get->a);
	} else if ($func->load_data($get->a, true)) {
		$func->load_data($get->a);
	} else {
		$content = $func->get_content($get->a);
		
		if (!$content['content']) {
			$func->error404();
		} else {
			$tpl->assign('content', $content);
		}
	}
?>