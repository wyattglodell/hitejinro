<?php
	if ($get->a != 'entry' && !Site::age_verified()) {
		$func->redirect($conf->https.'/entry');
	}

	if ( isset($get->site) && $conf->sites[$get->site] ) {
		Site::set_current_site($get->site);
	}
	
	$site = Site::get_current_site();

	if ($get->a != 'home' && !$site) {
		$func->redirect($conf->https.'/home');
	}

	$tpl->css('normalize.css', true);
	$tpl->css('main.css', true);
	$tpl->js('custom.js', '', true);

	$tpl->assign('favicon', $conf->public.'/img/favicon.png', true);
	$tpl->head_inc('<script>
  (function(d) {
    var config = {
      kitId: "iry6rtv",
      scriptTimeout: 3000,
      async: true
    },
    h=d.documentElement,t=setTimeout(function(){h.className=h.className.replace(/\bwf-loading\b/g,"")+" wf-inactive";},config.scriptTimeout),tk=d.createElement("script"),f=false,s=d.getElementsByTagName("script")[0],a;h.className+=" wf-loading";tk.src="https://use.typekit.net/"+config.kitId+".js";tk.async=true;tk.onload=tk.onreadystatechange=function(){a=this.readyState;if(f||a&&a!="complete"&&a!="loaded")return;f=true;clearTimeout(t);try{Typekit.load(config)}catch(e){}};s.parentNode.insertBefore(tk,s)
  })(document);
</script>');
	
	$site_data = Site::get_site_data($site);
	
	if (!$site) {
		$tpl->body_classes('split-choice');
		$tpl->set_template('body', 'home.tpl.php');		
	} else {
		$tpl->body_classes($site);
		$tpl->assign('current_site', $site);
		
		$tpl->assign('sites', Site::get_sites());
		$tpl->assign('site', $site);
		
		$tpl->assign('social_links', $site_data['social']);
		$tpl->assign('navigation',$site_data['pages']);
		$tpl->assign('footer_navigation',$site_data['footer_navigation']);
	}
?>