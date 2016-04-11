<?php
	$site_data = Site::get_site_data();

	$featured = Site::get_featured_pages($site_data['pages']);
	
	$brand_intro = $site_data['brand_intro'];

	$tpl->assign('featured', $featured);
	$tpl->assign('brand_intro', $brand_intro);

	$tpl->set_template('content', 'home.tpl.php');
?>