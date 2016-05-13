<?php
	ini_set('display_errors', true);
	error_reporting(E_ALL & ~E_NOTICE);
	
	$tpl->assign('management_header', 'Store Locations');
	
	if ($get->c == 'edit') {
		$func->load_data('admin/location/edit');	
	} else if ($get->c == 'import') {
		$func->load_data('admin/location/import');	
	} else if ($get->c == 'delete' && $get->d) {
		$location_id = intval($get->d);
		
		$this->sql->query("DELETE FROM ".$conf->STORE_LOCATOR." WHERE location_id = $location_id");
		
		$func->log('Store address has been deleted');
		
		$func->redirect($https.'/'.$get->a.'/'.$get->b, 'Store address has been removed successfully');	
	} else {
		if ($get->state) {
			$_SESSION['address_state'] = $get->state;
		}
		
		$sql->query("SELECT *, COUNT(*) as num FROM $conf->STORE_LOCATOR GROUP BY state");	
		while ($row = $sql->fetch())
		{
			if (!$states[ $row['state'] ]) {
				$states[ $row['state'] ] = $row['num'];
			}
		}
	#	$func->publish_store_addresses();
		
		if ($_SESSION['address_state']) {
			$sql->query("SELECT * FROM $conf->STORE_LOCATOR WHERE state = '".$sql->sanitize($_SESSION['address_state'])."' ORDER BY store_name");		
			while ($row = $sql->fetch())
			{
				$addresses[] = $row;
			}
		}
		
		$tpl->assign('states', $states);
		$tpl->assign('addresses', $addresses);
		$tpl->assign('state_selected', $_SESSION['address_state']);
		$tpl->assign('state_list', $func->get_list('states'));
	
		$tpl->set_template('admin', 'admin/locations.tpl.php');
	}
?>