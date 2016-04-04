<?php
	class Site
	{
		static $sites = array('hite', 'jinro');
		
		function set_site_data()
		{
			$_SESSION['data']['theme'] = array(
				'social' => array(
					'facebook' => 'https://www.facebook.com/hitejinro',
					'twitter' => 'https://twitter.com/HiteJinroUSA',
					'instagram' => 'https://www.instagram.com/hitejinrousa/'
				),
				'footer_links' => array(
					'terms & conditions' => '/terms-and-conditions',
					'privacy policy' => '/privacy-policy',
					'contact us' => '/contact-us',
				),
				'pages' => array(
					self::$sites[0] => array(
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
					),
					self::$sites[1] => array(
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
							'headline'=>'recipes',
							'link'=>'/recipes',
							'featured'=>true,
							'featuredOrder'=>2,
							'subtitle'=>'learn more',
							'img'=>'recipes.jpg'
						),
						array(
							'headline'=>'contact',
							'link'=>'/contact',
							'featured'=>false,
							'featuredOrder'=>null,
							'subtitle'=>'',
							'img'=>'',
						),
					),
				),
			);
		}

		function get_site_data()
		{
			return $_SESSION['data'];
		}

		function set_var($var, $val = '')
		{
			$_SESSION['site'][$var] = $val;
		}

		function get_var($var)
		{
			if (isset(	$_SESSION['site'][$var] )) {
				return $_SESSION['site'][$var];
			} else {
				return NULL;	
			}
		}

		function set_current_site($val)
		{
			$_SESSION['site'] = $val;
		}

		function get_current_site()
		{
			if ($_SESSION['site'] && in_array($_SESSION['site'], self::$sites)) {
				return $_SESSION['site'];
			} else {
				return NULL;
			}
		}
	}
?>