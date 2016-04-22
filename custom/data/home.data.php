<?php
	$site_data = Site::get_site_data();

	$featured = Site::get_featured_pages($site_data['pages']);
	
	$brand_intro = $site_data['brand_intro'];

	$tpl->assign('featured', $featured);
	$tpl->assign('brand_intro', $brand_intro);
	
	$site = Site::get_current_site();
	
	if ($site == 'hite') {
		$tpl->assign('instagram_hash', str_replace('#','', $setting->hite_instagram_hash_tag));
	} else if ($site == 'jinro') {
		$tpl->assign('instagram_hash', str_replace('#','', $setting->jinro_instagram_hash_tag));
	}

	$tpl->js('instafeed.min.js', '', true);
	$tpl->js('home.js', '', true);		
	$tpl->set_template('content', 'home.tpl.php');
?>