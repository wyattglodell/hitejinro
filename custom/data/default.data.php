<?php
	$site = Site::get_current_site();

	if (!$site) $func->redirect('/entry');
	
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