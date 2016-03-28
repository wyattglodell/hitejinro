<?php
	$admin->set_keyword('Crud Generator');

	if (!is_writable($conf->custom.'/data/admin/')) {
		$func->set_msg($conf->custom.'/data/admin/ is NOT writable!');	
	}
	
	if (!is_writable($conf->custom.'/inc/config.php')) {
		$func->set_msg($conf->custom.'/inc/config.php is NOT writable!');	
	}

	if ($_POST['action'] == 'Submit') {
		$_POST['can_add'] = (int) $_POST['can_add'];
		$_POST['can_edit'] = (int) $_POST['can_edit'];
		$_POST['can_delete'] = (int) $_POST['can_delete'];
		$_POST['description'] = str_replace('"', '&quot;', $_POST['description']);
		$manager = strtolower($_POST['manager_name']);
	
		$php = file_get_contents($conf->core.'/inc/example-crud.php');
		
		$php = str_replace('$admin->allow_add(1);','$admin->allow_add('.$_POST['can_add'].');', $php);
		$php = str_replace('$admin->allow_edit(1);','$admin->allow_edit('.$_POST['can_edit'].');', $php);
		$php = str_replace('$admin->allow_delete(1);','$admin->allow_delete('.$_POST['can_delete'].');', $php);
		$php = str_replace('$admin->set_keyword(\'Content\');','$admin->set_keyword(\''.addslashes($_POST['keyword']).'\');', $php);
		$php = str_replace('$admin->set_tbl($conf->CONTENT);','$admin->set_tbl($conf->'.strtoupper($_POST['table_reference']).');', $php);
		$php = str_replace('$admin->set_primary_key(\'content_id\');', '$admin->set_primary_key(\''.$_POST['primary_key'].'\');', $php);
		$php = str_replace('$admin->set_description("<p>Description</p>");', ($_POST['description'] ? '$admin->set_description(\''.addslashes($_POST['description']).'\');' : '#$admin->set_description(\'\');'), $php);
		
		if (file_exists($conf->custom.'/data/admin/'.$manager.'.data.ph')) {
			$func->reload('Manager already exists');
		} else {
			file_put_contents($conf->custom.'/data/admin/'.$manager.'.data.php', $php);
			
			$php = file_get_contents($conf->custom.'/inc/config.php');
			
			if (strpos($php, "\$conf->set('".strtoupper($_POST['table_reference'])."',\$conf->db_prefix.'".$_POST['table_name']."');") === false) {
				$php = str_replace('?>', "\t\$conf->set('".strtoupper($_POST['table_reference'])."',\$conf->db_prefix.'".$_POST['table_name']."');\n?>", $php);
				file_put_contents($conf->custom.'/inc/config.php', $php);
			}
			
			chmod($conf->custom.'/data/admin/'.$manager.'.data.php', 0777);
			
			$sql->query("
				CREATE TABLE IF NOT EXISTS `".$conf->db_prefix.$sql->sanitize(str_replace($conf->db_prefix,'',$_POST['table_name']))."` (
					`".$sql->sanitize($_POST['primary_key'])."` int(10) NOT NULL AUTO_INCREMENT,
					`name` varchar(250) NOT NULL DEFAULT '',
					`status` tinyint(4) NOT NULL,
					`weight` int(10) NOT NULL,
					PRIMARY KEY (`".$sql->sanitize($_POST['primary_key'])."`)
				) ENGINE=MyISAM  DEFAULT CHARSET=latin1");
			
			$func->reload('Manager has been created successfully in '.$conf->custom.'/data/admin/'.$manager.'.data.php');		
		}
	}
	
	$tpl->assign('prefix', $conf->db_prefix);
	
	$tpl->set_template('admin', 'admin/crud.tpl.php');
?>

