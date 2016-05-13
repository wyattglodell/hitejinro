<?php
	if ($setting->home_url && $setting->home_url != 'home') {
		$func->load_data($setting->home_url);
	} else {
		$content = $func->get_content($get->a, true);
		$tpl->assign('content', $content);
	}
?>
