<?php
	$tpl->assign('management_header', 'Store Locations');
	
	if ($_FILES['file']) {
		ini_set("auto_detect_line_endings", true);
		$f = fopen($_FILES['file']['tmp_name'], 'r');

		$sql->query("SELECT address, zip FROM $conf->STORE_LOCATOR WHERE store_id = '".Store::get_id()."'");
		while ($data = $sql->fetch())
		{
			$list[ strtolower(' '.$data['address'].','.$data['zip'])] = true;
		}

		$count = 0;
		while (($row = fgetcsv($f)) !== false)
		{
			if (trim($row[0])) {
				if (!$count++) { # check to see if the first row is a header
					foreach ($row as $v)
					{
						if (strpos($v, 'address') !== false) {
							continue 2;	
						}
					}
					
				}
	
				if (!$list[' '.strtolower($row[2].','.$row[6])]) {
					
					if (!$row['lat'] || !$row['lng']) {
						$address = $row[2].','.$row[3].','.$row[4].','.$row[5].','.$row[6];
					
						$resp_json = file_get_contents("https://maps.google.com/maps/api/geocode/json?key=AIzaSyDerTRI_Lr-7eQ8xPlSR0kKatsJzQfljqI&sensor=false&address=".urlencode($address));
						$resp = json_decode($resp_json, true);
			
						if ($resp['results'][0]['geometry']['location']['lat'] && $resp['results'][0]['geometry']['location']['lng']) {
							$row['geolocation'] = $resp['results'][0]['geometry']['location']['lat'].','.$resp['results'][0]['geometry']['location']['lng'];
							
						}
					} else {
						$row['geolocation'] = $row['lat'].','.$row['lng'];	
					}
					
					$row = $sql->sanitize($row);
	
					$sql->insert($conf->STORE_LOCATOR, [
						'store_id' => Store::get_id(),
						'store_name' => $row[1],
						'address' => $row[2],
						'address2' => $row[3],
						'city' => $row[4],
						'state' => $row[5],
						'zip' => $row[6],
						'country' => $row[7],
						'phone' => $row[8],
						'url' => $row[9],
						'hour1' => $row[10],
						'hour2' => $row[11],
						'hour3' => $row[12],
						'geolocation'=>$row['geolocation']
					]);
				} 
			} else {
				break;	
			}
		}
		
		$func->redirect($conf->https.'/'.$get->a.'/'.$get->b, ($count > 1 ? 'Import successful' : 'Nothing to import'));
	}
	
	
	
	
	
	
	
	$tpl->set_template('admin', 'admin/locations.tpl.php');
?>