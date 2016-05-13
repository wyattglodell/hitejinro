<?php
	if ($_FILES['file']) {
		ini_set("auto_detect_line_endings", true);
		$f = fopen($_FILES['file']['tmp_name'], 'r');

		$sql->query("SELECT * FROM $conf->STORE_LOCATOR");
		while ($data = $sql->fetch())
		{
			$list[ strtolower($data['store_name'].$data['address'].$data['zip'])] = $data;
		}

		$count = $insert_count = $duplicate = $update_count = 0;
		$has_header = false;
		
		while (($row = fgetcsv($f)) !== false)
		{
			if (trim($row[0])) {
				
				if (!$row[1] || !$row[3] || !$row[4] || !$row[5]) continue;				
				
				if (!$count++) { # check to see if the first row is a header
					foreach ($row as $v)
					{
						if (strpos($v, 'address') !== false) {
							$has_header = true;
							continue 2;	
						}
					}
				}
				
				foreach ($row as $k=>$v)
				{
					$row[$k] = trim(preg_replace('~[^\00-\255]~', '', $v));
				}
			
				$row[0] = strtoupper($row[0]);
				$row[7] = $row[7] == '(blank)' ? '' : $row[7];
				$row[5] = str_pad($row[5], 5, 0, STR_PAD_LEFT);
				
				if ($row[7]) {
					$row[7] = preg_replace('~[^0-9]~', '', $row[7]);
				}
				
				foreach (array(1,2,3) as $v)
				{
					$row[$v] = 	ucwords(strtolower($row[$v])); #\t\r\n\f\v
				}
				
				$formatted_address_id = strtolower($row[0].$row[1].$row[5]);
						
				if ($row[12] && $row[13]) {
					$row['geolocation'] = $row[12].','.$row[13];
				} else if (isset($list[$formatted_address_id]) && $list[$formatted_address_id]['geolocation']) {
					$row['geolocation'] = $list[$formatted_address_id]['geolocation'];	
				} else {
					$address = $row[1].','.$row[3].','.$row[4].','.$row[5].','.$row[6];
					
					$resp_json = file_get_contents("https://maps.google.com/maps/api/geocode/json?key=AIzaSyDAiZ0XyyNF7Cx6ezjCkJ0OEojEAsAh5wE&sensor=false&address=".urlencode($address));
					$resp = json_decode($resp_json, true);
				
					if ($resp['status'] == 'OVER_QUERY_LIMIT') {
						$func->redirect($conf->https.'/'.$get->a.'/'.$get->b, $resp['error_message']);	
						
					} else if ($resp['status'] == 'OK') {
						$row['geolocation'] = $resp['results'][0]['geometry']['location']['lat'].','.$resp['results'][0]['geometry']['location']['lng'];
					}
				}
				
				
				if ($row['geolocation']) {
					$insert = array(
							'store_name' => $row[0],
							'address' => $row[1],
							'address2' => $row[2],
							'city' => $row[3],
							'state' => $row[4],
							'zip' => $row[5],
							'country' => $row[6],
							'phone' => $row[7],
							'url' => $row[8],
							'hour1' => $row[9],
							'hour2' => $row[10],
							'hour3' => $row[11],
							'geolocation'=>$row['geolocation']
					);

					if (isset($list[$formatted_address_id])) {
						
						$diff = array_diff_assoc($list[$formatted_address_id],$insert);
						
						if (count($diff) > 1) {
							$update_count++;
							$sql->update($conf->STORE_LOCATOR, $sql->sanitize($insert), '', 'location_id', $list[$formatted_address_id]['location_id']);
						} else {
							$duplicate++;
						}
						
					} else {
						$insert_count++;
						$sql->insert($conf->STORE_LOCATOR, $sql->sanitize($insert));
					}
				}
			} else {
				break;	
			}
		}
		if ($has_header) $count--;
		
		if ($count) {
			if ($update_count || $insert_count) {
				$func->publish_store_addresses();	
			}
			
			$msg = $insert_count.' added  / '.$update_count.' updated / '.$duplicate.' duplicates ignored successfully';	
		} else {
			$msg = 'Could not find anything to import';	
		}
		
		$func->redirect($conf->https.'/'.$get->a.'/'.$get->b, $msg);
	}



	$tpl->set_template('base', 'admin/location/import.tpl.php');
?>