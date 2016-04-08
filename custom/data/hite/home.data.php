<?php
	$pages = array(
		array(
			'headline'=>'about',
			'link'=>'/about',
			'featured'=>false,
			'featuredOrder'=>null,
			'subtitle'=>'',
			'img'=>'',
		),
		array(
			'headline'=>'products',
			'link'=>'/products',
			'featured'=>true,
			'featuredOrder'=>1,
			'subtitle'=>'view all',
			'img'=>'products.jpg'
		),
		array(
			'headline'=>'news & events',
			'link'=>'/news-and-events',
			'featured'=>true,
			'featuredOrder'=>3,
			'subtitle'=>'learn more',
			'img'=>'news_events.jpg'
		),
		array(
			'headline'=>'where to buy',
			'link'=>'/where-to-buy',
			'featured'=>true,
			'featuredOrder'=>0,
			'subtitle'=>'view locations',
			'img'=>'where_to_buy.jpg',
		),
		array(
			'headline'=>'fan page',
			'link'=>'/fan-page',
			'featured'=>true,
			'featuredOrder'=>2,
			'subtitle'=>'learn more',
			'img'=>'fan.jpg'
		),
		array(
			'headline'=>'contact',
			'link'=>'/contact',
			'featured'=>false,
			'featuredOrder'=>null,
			'subtitle'=>'',
			'img'=>'',
		),
	);

	$featured = array_filter($pages, function($arr) {
		return $arr['featured'];
	});

	usort($featured, function($a, $b) {
		return strcmp($a['featuredOrder'], $b['featuredOrder']);
	});

	$brand_content = array(
		'headline' => 'It\'s more than <span>just beer</span>',
		'description' => 'Hite is Korea\'s largest and leading brewery with approximately 60% of the nation\'s domestic beer market share. Hite has been the number one beer brand in Korea for more than 16 years, and now exports its product to loyal Hite customers in more than 50 countries around the world!',
		'img' => 'home_brand.png'
	);

	$tpl->assign('navigation', $pages);
	$tpl->assign('featured', $featured);
	$tpl->assign('brand_content', $brand_content);
?>