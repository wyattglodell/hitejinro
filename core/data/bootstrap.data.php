<?php	
	$tpl = new Template;
	Registry::set($tpl, 'tpl');

	$tpl->set_template('base', 'base.tpl.php'); 
	
	$tpl->set_template('head', 'head.tpl.php');
	$tpl->set_template('body', 'body.tpl.php');
	
	$tpl->set_template('header', 'header.tpl.php');
	$tpl->set_template('footer', 'footer.tpl.php');	
	$tpl->set_template('content', 'content.tpl.php');	
		
	$tpl->base_js('core.js');
	$tpl->base_css('core.css');
	
	$tpl->assign('page', $get->a);
	$tpl->assign('subpage', $get->b);
	$tpl->assign('url', $conf->current_url);
	$tpl->assign('uri', $conf->current_uri);
	$tpl->assign('site_name', $setting->site_name);
	$tpl->assign('path', $conf->path);
	$tpl->assign('http', $conf->http);
	$tpl->assign('https', $conf->https);
	$tpl->assign('ssl', $conf->ssl);
	
	$tpl->assign('img', $conf->img);
	$tpl->assign('captcha_url', $conf->captcha);	

	$tpl->assign('metadescription', htmlspecialchars( $setting->meta_description, ENT_QUOTES));
	$tpl->assign('metakeyword', htmlspecialchars( $setting->meta_tag, ENT_QUOTES));
	$tpl->assign('metatitle',  $setting->site_name);

	$tpl->assign('favicon', $conf->base.'/img/favicon.ico');
	
	$tpl->js_var('http', $conf->https);
	$tpl->js_var('url', $conf->current_url);
	
	$tpl->body_classes($get->a);
	$tpl->body_classes($get->a == $conf->admin_alias ? 'back' : 'front');
	
	if ($get->b) {
		$tpl->body_classes($get->a.'-'.$get->b);
	}
	
	if ($get->c) {
		$tpl->body_classes($get->a.'-'.$get->b.'-'.$get->c);
	}
	
	if (User::logged_in()) {
		$tpl->body_classes('logged-in');
		
		$tpl->js_var('session_expire', intval(ini_get('session.gc_maxlifetime')));
	}
		
	if (!in_array($get->a, array($conf->filemanager_alias,$conf->admin_alias,'install-setting','install'))) {
		$func->load_data('pre_load');
	}
	
	$loaded = $func->load_data($get->a);
	
	if (!$loaded && $get->b) {
		$loaded = $func->load_data($get->a.'/'.$get->b);	
	}
	
	if (!$loaded) {
		$func->load_data('default');	
	}

	$func->load_data('post_load');
	
	$tpl->display();
?>