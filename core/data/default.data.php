<?php
	$content = $func->get_content($get->a);

	if (!$content['content']) {
		$func->error404();
	} else {
		$tpl->assign('content', $content);
	}
?>