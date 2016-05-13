<?php
	class Site
	{
		function get_sites()
		{
			return array_keys($this->conf->sites);
		}
		
		function get_site_data($site)
		{
			if (!$site) {
				$site = Site::get_current_site();
			}
			
			$site_data = array(
				'hite' => array(
					'pages' => array(
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
							'img'=>'fan.jpg'
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
							'featuredTitle'=>'Hite Jinro Fan Page',
							'subtitle'=>'learn more',
							'img'=>'products.jpg'
						),
						array(
							'headline'=>'contact',
							'link'=>'/contact',
							'featured'=>false,
							'featuredOrder'=>null,
							'subtitle'=>'',
							'img'=>'',
						)
					),
					'brand_intro' => array(
						'headline' => 'It\'s more than <span>just beer</span>',
						'description' => 'Hite is Korea\'s largest and leading brewery with approximately 60% of the nation\'s domestic beer market share. Hite has been the number one beer brand in Korea for more than 16 years, and now exports its product to loyal Hite customers in more than 50 countries around the world!',
						'img' => 'home_brand.jpg'
					)
				),
				'jinro' => array(
					'pages' => array(
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
							'featuredTitle'=>'Hite Jinro Fan Page',
							'featuredLink'=>'/fan-page',
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
						)
					),
					'brand_intro' => array(
						'headline' => 'Number one <span>soju brand</span>',
						'description' => 'Since its launch in 1924, Jinro has consistently held is position as the number one soju brand in Korea. As such, it is not an exaggeration to say that Jinro Soju has played a pivotal role in establishing and evolving Korea\'s drinking culture.',
						'img' => 'home_brand.png'
					)
				),
			);

			$temp = $site_data[$site];
			
			
			$temp['social'] = array(
				'heart' => '/fan-page',
				'facebook' => 'https://www.facebook.com/hitejinro',
				'twitter' => 'https://twitter.com/HiteJinroUSA',
				'instagram' => array(
					'hite' => 'https://www.instagram.com/HiteJinroUSA/',
					'jinro' => 'https://www.instagram.com/HiteJinroUSA/',
				)
			);
				
			$temp['footer_navigation'] = array(
				'terms & conditions' => '/terms-and-conditions',
				'privacy policy' => '/privacy-policy',
				'contact us' => '/contact',
			);
			
			return $temp;
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
		
		function set_age($min_age)
		{
			$_SESSION['age'] = $min_age;
		}
		
		function age_verified()
		{
			return $_SESSION['age']	>= 21;
		}

		function set_current_site($val)
		{
			$_SESSION['site'] = $val;
		}

		function get_current_site()
		{
			return $_SESSION['site'];
		}

		function get_featured_pages($pages)
		{
			$featured = array_filter($pages, function($arr) {
				return $arr['featured'];
			});

			usort($featured, function($a, $b) {
				return strcmp($a['featuredOrder'], $b['featuredOrder']);
			});

			return $featured;
		}
	}
?>