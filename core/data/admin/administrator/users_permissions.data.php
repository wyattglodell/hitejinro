<?php
	$admin->set_keyword('User Permissions');
	$admin->set_tbl($conf->USER_PERMISSION);
	$admin->set_primary_key('permission_id');
	
	$admin->set_parent('administrator:user_roles', 'name', $conf->ROLE, 'role_id', 'rid');	
	
	if (user_level(0) && $get->delete) {
		$id = (int) $get->delete;
		
		$admin->ids = $id;
		$admin->allow_delete(1);	
		
		$admin->delete();
	}
	
	$rs = $sql->query("SELECT * FROM $conf->USER_PERMISSION ORDER BY `group`, `action`");
	while ($row = $sql->fetch($rs))
	{
		$permissions[$row['group']][$row['permission_id']] = $row['action'];	
	}

	$sql->query("SELECT * FROM $conf->ROLE WHERE role_id > 1 AND role_id = ".intval($get->rid)." ORDER BY level");
	while ($row = $sql->fetch())
	{
		$roles_level [ $row['role_id'] ] = $row['level'];	
		$roles[] = $row;
	}
		
	$tpl->assign('roles', $roles);


		
	if ($_POST['action'] == 'permissions') {
		$wiped = false;
		
		foreach ($_POST['perm'] as $rows)
		{
			foreach ($rows as $role_id=>$row)
			{
				$role_id = (int) $role_id;
				
				if ($roles_level[$role_id] <= User::get_level()) {
					$func->log('Tried to manage a role of lower or equal level', $_POST, 'severe');
				}
				
				if (!$wiped) {
					$sql->query("DELETE FROM $conf->ROLE_PERMISSION WHERE role_id = $role_id AND role_id > 1");
					$wiped = true;	
				}
				
				foreach ($row as $pid=>$v)
				{
					$pid = (int) $pid;
					
					$sql->query("INSERT INTO $conf->ROLE_PERMISSION (role_id, permission_id) VALUES ($role_id, $pid)");
				}
			}
		}

		$func->log('User permissions has been updated');
		
		$func->redirect($conf->admin.'/'.$admin->get_manager().'?rid='.$get->rid, "User permissions table has been updated");
	}
	
	$sql->query("SELECT * FROM $conf->ROLE_PERMISSION");
	while ($row = $sql->fetch())
	{
		$role_permission[$row['role_id']][$row['permission_id']] = true;	
	}
		
	$tpl->assign('role_permission', $role_permission);
	$tpl->assign('permissions', $permissions);
	
	$tpl->assign('form_url', $conf->admin.'/'.$admin->get_manager().'?rid='.$get->rid);
	$tpl->assign('rid', $get->rid);
	$tpl->assign('title', 'User Permissions');

	$tpl->set_template('admin', 'admin/user-permission.tpl.php');
?>