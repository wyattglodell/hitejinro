<?php
 	$edit = $get->c == 'edit' && $get->d ? true : false;
	
	$admin = new Admin;

	$admin->allow_add(1);
	$admin->allow_edit(1);
	$admin->set_primary_key('location_id');
	$admin->set_tbl($conf->STORE_LOCATOR);
	$admin->set_keyword('Store Address');
	
	foreach ($func->get_list('countries') as $v)
	{
		$countries[$v] = $v;
	}
		
	$admin->form_field('country','Country','select', array('required'=>1, 'list'=>$countries, 'empty' => 'United States'));
	$admin->form_field('store_name','Store Name','text', array('required'=>1, 'empty'=>$store['store_name']));
	$admin->form_field('address','Address','text', 'required');
	$admin->form_field('address2','Address 2','text', '');
	$admin->form_field('city','City','text', 'required');
	$admin->form_field('state','State','text', 'required');
	$admin->form_field('zip','Zip Code','text', 'required');
	$admin->form_field('phone','Phone Number','text', '');
	$admin->form_field('url','Website URL','text', '');
	$admin->form_field('hour1','Store Hour','text', '');
	$admin->form_field('hour2','Store Hour','text', '');
	$admin->form_field('hour3','Store Hour','text', '');
	
	if ($_POST['action'] == 'submit') {
		$admin->id = (int) $get->d;
		
		if ($edit) {
			$sql->query("SELECT COUNT(*) FROM $admin->tbl WHERE $admin->pk = $admin->id");
			if (!$sql->result()) {
				$func->log("Invalid location ID");
				$func->redirect($conf->https.'/'.$get->a.'/'.$get->b, 'Invalid location ID');
			}
		}
		
		foreach ($admin->form as $group=>$row)
		{
			foreach ($row as $k=>$v)
			{
				$post[$k] = $_POST[$k];
			}	
		}
		
		$post = $admin->process($post);
		
		if ($admin->action != 'return') {
			$address = $post['address'].','.$post['address2'].','.$post['city'].','.$post['state'].','.$post['zip'].','.$post['country'];
			
			$resp_json = file_get_contents("https://maps.google.com/maps/api/geocode/json?key=AIzaSyDerTRI_Lr-7eQ8xPlSR0kKatsJzQfljqI&sensor=false&address=".urlencode($address));
   			$resp = json_decode($resp_json, true);

			if ($resp['results'][0]['geometry']['location']['lat'] && $resp['results'][0]['geometry']['location']['lng']) {
				$post['geolocation'] = $resp['results'][0]['geometry']['location']['lat'].','.$resp['results'][0]['geometry']['location']['lng'];
			}
						
			if ($edit) {	
				$resp = $admin->update($post, true);
			} else {
				$resp = $admin->insert($post, true);
			}
			
			if ($resp['status']) {
				$func->redirect($conf->https.'/'.$get->a.'/'.$get->b, $resp['msg']);
			} else {
				$func->set_msg($resp['msg']);
			}
		} else {
			$images = $_POST;
		}
	} else {
		if ($edit) {
			$id = (int) $get->d;
			
			$sql->query("SELECT * FROM $admin->tbl WHERE $admin->pk = $id");
			$data = $sql->fetch();
		}
	}

	
	$widget[] = $admin->build_form_table('country', $data);
	$widget[] = $admin->build_form_table('store_name', $data);
	$widget[] = $admin->build_form_table('address', $data);
	$widget[] = $admin->build_form_table('address2', $data);
	$widget[] = $admin->build_form_table('city', $data);
	$widget[] = $admin->build_form_table('state', $data);
	$widget[] = $admin->build_form_table('zip', $data);
	$widget[] = $admin->build_form_table('phone', $data);
	$widget[] = $admin->build_form_table('url', $data);
	$widget[] = $admin->build_form_table('hour1', $data);
	$widget[] = $admin->build_form_table('hour2', $data);
	$widget[] = $admin->build_form_table('hour3', $data);

	
	$tpl->assign('widget', $widget);
	
	
	$tpl->css('admin-widget.css');
	
	
	if ($edit) {	
		$tpl->assign('page_title', 'Edit Address', true);
	} else {
		$tpl->assign('page_title', 'Add New Address', true);
	}
	
	$tpl->set_template('admin', 'admin/location/edit.tpl.php');
?>