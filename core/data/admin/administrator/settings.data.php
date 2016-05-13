<?php
	if (user_access('Manage System Settings', 'Settings')) {
		$where = "WHERE `group` != 'hidden'";
		$can_add = 1;
		
		$tpl->assign('can_add', $can_add);
		$admin->allow_add($can_add);
	} else {
		$where = "WHERE `group` != 'system' AND `group` != 'hidden'";
	}
	
	if ($get->flush_cache && user_access('Empty Page Cache', 'Settings')) {
		$f = glob($conf->cache_file.'/*'); 
		if ($f) {
			foreach ($f as $v)
			{
				if (is_file($v)) {
					unlink($v);
				}
			}
		}
		
		$sql->query("DELETE FROM $conf->CACHE_PAGE WHERE host = '".$_SERVER['HTTP_HOST']."'");
		$sql->query("DELETE FROM $conf->CACHE WHERE host = '".$_SERVER['HTTP_HOST']."'");
		
		$func->log("Cache cleared");
		$func->reload('Page cache has been cleared');	
	}
	
	$admin->set_keyword('Settings');
	
	if ($admin->action == 'add') {
		$sel = array('text'=>'Text','textarea' => 'Text Area','select' => 'Select','file'=>'File');
	
		$admin->form_field('group','Group','text');
		$admin->form_field('name','Name','text');
		$admin->form_field('type', 'Type', 'select', $sel);
		$admin->form_field('options','Options','textarea', array('required'=>false));
		$admin->form_field('info','Info','textarea', array('required'=>false));
		$admin->form();
	} else {
	
		if ($admin->action == 'submit_add') {
			$c = $sql->sanitize($_POST);
			if ($can_add) {
				$c['name'] = $func->clean_url($c['name'],'_');
				
				$sql->query("INSERT INTO $conf->SITE_SETTING (`group`,name,value,type,options,info) VALUES ('$c[group]','$c[name]','$c[value]','$c[type]','$c[options]','$c[info]')");
				$func->reload('Setting has been added successfully.');
			}
		} else if ($admin->action == 'update_settings') {
			$ignore = array('action');

			foreach ($_POST as $k=>$v)
			{
				if (!in_array($k, $ignore)) {
					$sql->query("UPDATE $conf->SITE_SETTING SET value = '".$sql->sanitize($v)."' WHERE name = '".$sql->sanitize($k)."'");
				}
			}
			
			$func->reload('Settings have been updated successfully.');
		}
		
		
		$sql->query("SELECT * FROM $conf->SITE_SETTING $where");
		while ($row = $sql->fetch())
		{
			if (!$row['group']) $row['group'] = 'General';
			
			$form[$row['group']][$row['setting_id']]['label'] = ucwords(str_replace('_',' ', $row['name'])); 
			$form[$row['group']][$row['setting_id']]['name'] = $row['name']; 
			$form[$row['group']][$row['setting_id']]['type'] = $row['type']; 
			$form[$row['group']][$row['setting_id']]['value'] = $row['value']; 
			
			$field = '';
			
			if ($row['type'] == 'select') {
				$row['options'] = explode('|', $row['options']);
				
				$field = "<select name='$row[name]'>";
				foreach ($row['options'] as $v)
				{
					$sel = ($row['value'] == $v) ? "selected='selected'" : '';
					$field .= "<option value='$v' $sel>$v</option>";
				}
				
				$field .= "</select>";
			} else if ($row['type'] == 'textarea') {
				$field = "<textarea name='$row[name]' rows='3' cols='50'>".htmlspecialchars($row['value'])."</textarea>";
			} else if ($row['type'] == 'file') {
				++$fm_field_count;
				
				if ($row['value'] && file_exists($this->conf->root.$row['value']) && getimagesize($this->conf->root.$row['value'])) {
					$img = "<img src='".$this->conf->path.$row['value']."' height='30' />";
				} else if ($data) {
					$img = $row['value'];
				}
				
				$field = "
					<input type='hidden' name='$row[name]' id='fm-field-{$fm_field_count}' value='".htmlentities($value, ENT_QUOTES)."' class='$required' />
					<span id='fm-preview-{$fm_field_count}' class='pad1'>
						$img
					</span> &nbsp; 
					<span class='nudge'>
						<input type='button' value='Browse..' onclick='open_fm(\"fm-field-{$fm_field_count}\", \"fm-preview-{$fm_field_count}\")' class='btn' />
						<input type='button' value='Clear' onclick=\"document.getElementById('fm-field-{$fm_field_count}').value=''; document.getElementById('fm-preview-{$fm_field_count}').innerHTML = '';\" class='btn'/>
					</span>					
				";			
			} else {
				$field = "<input type='text' class='text' name='$row[name]' value='".htmlspecialchars($row['value'], ENT_QUOTES)."' />";
			}
			
			$form [$row['group']][$row['setting_id']]['field'] = $field;
			
			$form [$row['group']][$row['setting_id']]['info'] = $row['info']; 
		}
	
		$tpl->assign('form', $form);
		
		$tpl->assign('can_clear_cache', user_access('Empty Page Cache', 'Settings'));
		
		$tpl->set_template('admin', 'admin/settings.tpl.php');
	}
?>