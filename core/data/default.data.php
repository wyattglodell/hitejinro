<?php
	$content = $func->get_content($get->a);

	if (!$content['content']) {
		header('HTTP/1.1 404 Not Found');
		$tpl->set_template('content', 'page-not-found.tpl.php');
	} else {
		$tpl->assign('content', $content);
	}
?>