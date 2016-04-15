<?php
	$site = Site::get_current_site();
	$content = $func->get_content($get->a);
	if (!$content['content']) {
		$func->error404();
	} else {
		if (!$site) $tpl->assign('current_site', 'public');
		$tpl->assign('content', $content);
	}
?>