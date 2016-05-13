<?php
	if ($get->c && $get->c == 'permissions') {
		$func->load_data('admin/administrator/users_permissions');
	} else {
		function min_level($data)
		{
			$admin = Registry::get('admin');

			if ($data['level'] <= User::get_level()) {
				$admin->form_return("You cannot create a role with a level less than or equal to ".User::get_level());	
			}
			
			return $data['level'];
		}

		$admin->allow_add(1);
		$admin->allow_delete(1);	
		$admin->allow_edit(1);
	
		$admin->query_filter("level > 0");
	
		$admin->set_keyword('User Role');
		$admin->set_tbl($conf->ROLE);
		$admin->set_primary_key('role_id');
		
		function perm_link($data)
		{
			$conf = Registry::get('conf');
			return "<a href='".$conf->admin.'/administrator:users_permissions?rid='.$data['role_id']."'>Edit Permissions</a>";
		}
		
		$admin->add_field('name','Name', '200|edit|sort');
		$admin->add_field('role_id','Edit Permissions', '200|html|center|callback=perm_link');
		$admin->add_field('level','Level', '50|html|center|sort');
		
		$admin->form_field('name', 'Name', 'text', 'required');
		$admin->form_field('level', 'Level', 'weight', 'required|callback:min_level');

		$admin->action();
	}
?>