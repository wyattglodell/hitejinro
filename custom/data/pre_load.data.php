<?php
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
	
	if ($get->a != 'entry' && !Site::age_verified()) {
		$func->redirect($conf->https.'/entry');
	}

	Site::set_site_data();

	$site_data = Site::get_site_data();

	$tpl->assign('site_data', $site_data);
?>