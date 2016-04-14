<?php
	$user = new User;
	
	$admin->allow_add(1);
	$admin->allow_delete(1);	
	$admin->allow_edit(1);

	$admin->set_keyword('User');
	$admin->set_tbl($conf->USER);
	$admin->set_primary_key('user_id');
	
	$admin->set_ordering('create_dt DESC');
	$admin->add_dependency($conf->USER_ROLE);
	
	if ($get->switch && user_level(0) && $get->uid) {
		$uid = (int) $get->uid;
		
		$sql->query("SELECT * from $conf->USER WHERE user_id = '$uid'");
		$row = $sql->fetch();
		
		if ($row) {
			unset($row['password'],$row['temp_password']);
			
			$temp = $user->get_user();
			
			$user->set_user($row);
			
			$_SESSION['real_user'] = $temp;
			$func->log("User account switched to ".$user->get_username());
			
			$func->redirect($conf->admin, "User account switched to ".$user->get_username());	
		}	
	} else if ($get->reset_password && $get->uid) {
		$uid = (int) $get->uid;
		
		$sql->query("SELECT * from $conf->USER WHERE user_id = '$uid'");
		$row = $sql->fetch();
		
		if ($row) {
			$user->forgot_password($row, $conf->https.$conf->admin.'/reset-password');
			
			$func->log("User password reset link sent to ".$row['email']);
			die('Password reset notification has been emailed to '.$row['email']);	
		}
		
		die('No user data found');	
	}

	$sql->query("SELECT * FROM $conf->ROLE");
	while ($row = $sql->fetch())
	{
		if (user_level(0)) {
			$roles[$row['role_id']] = $row['name'];	
		} else if ($row['level'] >= User::get_level()) {
			$roles[$row['role_id']] = $row['name'];	
		} 
		
		$role_data[$row['role_id']] = $row;
		
		$roles_map[$row['role_id']] = $row['name'];
	}
	
	$sql->query("SELECT * FROM $conf->USER_ROLE ORDER BY weight");
	while ($row = $sql->fetch())
	{
		$user_roles[$row['user_id']][] = $row['role_id'];	
	}

	
	$conf->set('role_data', $role_data);
	$conf->set('roles_map', $roles_map);
	$conf->set('roles', $roles);
	$conf->set('user_roles', $user_roles);
	
	function user_roles($data)
	{
		$conf = Registry::get('conf');
		
		$string = array();
		
		foreach ((array) $conf->user_roles[$data['user_id']] as $role_id)
		{
			$string[] = $conf->roles_map[ $role_id ];
		}
		
		return implode('<br>', $string);
	}
	
	function user_edit($data)
	{
		$conf = Registry::get('conf');
		$admin = Registry::get('admin');
		
		$string = array();
		$edit = false;
		
		foreach ((array) $conf->user_roles[$data['user_id']] as $role_id)
		{
			if ($conf->role_data[$role_id]['level'] < User::get_level()) {
				return false;
			} 
		}
		
		return "<a href='".$admin->page("action=edit&id=$data[user_id]")."'>$data[username]</a>";
	}
	
	$admin->add_search('username', 'Username', 'text');
	$admin->add_search('email', 'Email Address', 'text');
	
	$admin->add_field('username','Username', '200|sort|html|callback=user_edit');
	$admin->add_field('roles','Roles', '200|sort|html|callback=user_roles');
	$admin->add_field('first_name','First Name', '200|left|sort');
	$admin->add_field('last_name','Last Name', '200|left|sort');
	$admin->add_field('email','Email Address', '200|left|sort');
	$admin->add_field('create_dt','Date Created', '200|sort|datetime');
	$admin->add_field('last_login_dt','Last Login', '200|sort|datetime');
	$admin->add_toggle('status','Status');
	
	if (user_level(0)) {
		$admin->add_link($admin->page('switch=1'), 'uid=', 'icon-users2', 'Switch',''); # pass filter=page_id:2 to populate filtering, type-redirect|type-blank|type-delete|type-hide|type-toggle
		$admin->add_link($admin->page('reset_password=1'), 'uid=', 'icon-key', 'Password&nbsp;Reset','confirm ajax control'); # pass filter=page_id:2 to populate filtering, type-redirect|type-blank|type-delete|type-hide|type-toggle
	}	
	
	$admin->form_field('username', 'Username', 'text', 'required|unique');
	$admin->form_field('password', 'Password', 'password');
	
	$admin->form_field('role_id', 'Roles', 'select', 'required|table:'.$conf->USER_ROLE.'|repeat', $roles);
	
	$admin->form_field('first_name', 'First Name', 'text', 'required');
	$admin->form_field('last_name', 'Last Name', 'text', 'required');
	$admin->form_field('email', 'Email', 'email', 'required|unique');

	function pre_process($data)
	{
		$admin = Registry::get('admin');
		$conf = Registry::get('conf');
		$func = Registry::get('func');
		$sql = Registry::get('sql');
		
		if ($data['username'] == $conf->master_username) {
			$admin->form_return('The username '.$data['username'].' cannot be used');	
		}

		if ($admin->action == 'submit_add') {
			if (!$data['password']) {
				$admin->form_return('Password is required on new users');	
			}	
			
			$data['create_dt'] = date('Y-m-d H:i:s');
		}
		
		if ($data['role_id']) {
			foreach ($data['role_id'] as $rid) {
				if ($rid && !$conf->roles[$rid] && $admin->id != User::get_user_id()) {
					$func->log('Tried to manage a role of higher level', $data, 'notice');
					die ("You don't have the permissions to complete this action");	
				}
			}
		}
		
		if ($admi->action == 'submit_edit') {
			$id = (int) $admin->id;
			$sql->query("SELECT MIN(level) FROM $conf->USER_ROLE ur LEFT JOIN $conf->USER u USING (user_id) LEFT JOIN $conf->ROLE r ON ur.role_id = r.role_id WHERE u.user_id = '$id'");
			$level = $sql->result();
			
			if ($level < User::get_level()) {
				die ("You can't update users of lower level");	
			}			
		} else if ($admin->action == 'delete') {
			$id = (int) $admin->ids;
			
			$sql->query("SELECT MIN(level) FROM $conf->USER_ROLE ur LEFT JOIN $conf->USER u USING (user_id) LEFT JOIN $conf->ROLE r ON ur.role_id = r.role_id WHERE u.user_id = '$id'");
			$level = $sql->result();
			
			if (User::get_user_id() == $id) {
				die("You can't delete your own account");	
			} else if ($level < User::get_level()) {
				die("You can't delete a user of lower level");	
			}
		} else if ($admin->action == 'toggle') {
			$id = (int) $admin->id;
			
			$sql->query("SELECT MIN(level) FROM $conf->USER_ROLE ur LEFT JOIN $conf->USER u USING (user_id) LEFT JOIN $conf->ROLE r ON ur.role_id = r.role_id WHERE u.user_id = '$id'");
			$level = $sql->result();
			
			if (User::get_user_id() == $id) {
				die("You can't change the status of yourself");	
			} else if ($level < User::get_level()) {
				die ("You can't change the status of a user of the lower level");	
			}
		}
						
		return $data;	
	}
		
	$admin->action();
?>