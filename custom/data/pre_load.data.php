<?php
	$tpl->css('normalize.css', true);
	$tpl->css('main.css', true);
	$tpl->js('https://fb.me/react-0.14.7.js', '', true);
	$tpl->js('https://fb.me/react-dom-0.14.7.js', '', true);
	$tpl->js('https://cdnjs.cloudflare.com/ajax/libs/babel-core/5.8.34/browser.js', '', true);
	$tpl->js('custom.js', 'babel', true);

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

	Site::set_site_data();

	$site_data = Site::get_site_data();

	$tpl->assign('site_data', $site_data);
?>