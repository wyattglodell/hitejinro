<?php
	class Func extends BaseFunc
	{
		function admin_menu_ignore()
		{
			$ignore = array(); # ex: $ignore[] = 'page';
			$ignore[] = 'edit';
			$ignore[] = 'import';
			
			return $ignore;
		}
		
		function admin_menu_icons()
		{
			$icons = array (
				'administrator' => 'icon-user7',
				'crud' => 'icon-tools',
				'settings' => 'icon-cog3',
				'menu' => 'icon-menu',
				'log' => 'icon-file',
				'menu' => 'icon-tree5',
				'users' => 'icon-user-plus2',
				'icons' => 'icon-lab',
				'recipe' => 'icon-food2', 
				'locations' => 'icon-location2',
				'user_roles' => 'icon-users2',
				'webforms' => 'icon-insert-template',
				'product' => 'icon-bottle2',
				'delete_archive' => 'icon-remove2',
				'fan_page' => 'icon-baseball',
				'news' => 'icon-mic',
				'pages' => 'icon-stack-empty'
			);
			
			return $icons;
		}
		
		function truncate($text, $length = 100, $options = array()) {
			$default = array(
				'ending' => '...', 'exact' => true, 'html' => false
			);
			$options = array_merge($default, $options);
			extract($options);
		
			if ($html) {
				if (mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
					return $text;
				}
				$totalLength = mb_strlen(strip_tags($ending));
				$openTags = array();
				$truncate = '';
		
				preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);
				foreach ($tags as $tag) {
					if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2])) {
						if (preg_match('/<[\w]+[^>]*>/s', $tag[0])) {
							array_unshift($openTags, $tag[2]);
						} else if (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag)) {
							$pos = array_search($closeTag[1], $openTags);
							if ($pos !== false) {
								array_splice($openTags, $pos, 1);
							}
						}
					}
					$truncate .= $tag[1];
		
					$contentLength = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3]));
					if ($contentLength + $totalLength > $length) {
						$left = $length - $totalLength;
						$entitiesLength = 0;
						if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE)) {
							foreach ($entities[0] as $entity) {
								if ($entity[1] + 1 - $entitiesLength <= $left) {
									$left--;
									$entitiesLength += mb_strlen($entity[0]);
								} else {
									break;
								}
							}
						}
		
						$truncate .= mb_substr($tag[3], 0 , $left + $entitiesLength);
						break;
					} else {
						$truncate .= $tag[3];
						$totalLength += $contentLength;
					}
					if ($totalLength >= $length) {
						break;
					}
				}
			} else {
				if (mb_strlen($text) <= $length) {
					return $text;
				} else {
					$truncate = mb_substr($text, 0, $length - mb_strlen($ending));
				}
			}
			if (!$exact) {
				$spacepos = mb_strrpos($truncate, ' ');
				if (isset($spacepos)) {
					if ($html) {
						$bits = mb_substr($truncate, $spacepos);
						preg_match_all('/<\/([a-z]+)>/', $bits, $droppedTags, PREG_SET_ORDER);
						if (!empty($droppedTags)) {
							foreach ($droppedTags as $closingTag) {
								if (!in_array($closingTag[1], $openTags)) {
									array_unshift($openTags, $closingTag[1]);
								}
							}
						}
					}
					$truncate = mb_substr($truncate, 0, $spacepos);
				}
			}
			$truncate .= $ending;
		
			if ($html) {
				foreach ($openTags as $tag) {
					$truncate .= '</'.$tag.'>';
				}
			}
		
			return $truncate;
		}	
			
		function utf8ize($mixed) 
		{
			if (is_array($mixed)) {
				foreach ($mixed as $key => $value) {
					$mixed[$key] = $this->utf8ize($value);
				}
			} else if (is_string ($mixed)) {
				return utf8_encode($mixed);
			}
			return $mixed;
		}
		
		function publish_store_addresses()
		{
			$this->sql->query("SELECT * FROM ".$this->conf->STORE_LOCATOR);
			$count = 0;
			$locations = array();
			
			while ($row = $this->sql->fetch())
			{
				$lat = explode(',', $row['geolocation']);

				array_push($locations, array(
					'id' => ++$count,
					'name' => $row['store_name'],
					'address'=>$row['address'],
					'address2'=>$row['address2'],
					'city'=>$row['city'],
					'state'=>$row['state'],
					'postal'=>$row['zip'],
					'country'=>$row['country'],
					'phone'=>Format::phone($row['phone']),
					'web'=>$row['url'],
					'hours1'=>$row['hour1'],
					'hours2'=>$row['hour2'],
					'hours3'=>$row['hour3'],
					'lat' =>$lat[0],
					'lng'=>$lat[1]
				));
			}
			
			if ($locations) {
				$json = json_encode($this->utf8ize($locations));
			}
			
			file_put_contents($this->conf->public_file.'/inc/store-addresses.json', $json);
		}
	}
?>