<?php
	$admin->set_keyword('Archive');
	$admin->set_tbl($conf->DELETE_ARCHIVE);
	$admin->set_primary_key('delete_id');

	$admin->allow_add(0);
	$admin->allow_edit(0);
	$admin->allow_delete(0);
	
	if ($get->detail) {
		if (user_level(0)) {
			$id = (int) $get->pid;
			$sql->query("SELECT * FROM $admin->tbl WHERE $admin->pk = '$id'");
			$row = $sql->fetch();
			
			if ($row['data']) {
				$data = unserialize(gzuncompress($row['data']));
			}
			
			echo '<pre>';			
			if (is_array($data)) {
				print_r($sql->sanitize($data, ENTITY));
			} else if ($data) {
				echo $sql->sanitize($data, ENTITY);
			} else {
				echo "<p style='width: 300px; padding; 20px;' class='center'>No additional data found</p>";	
			}
			
			echo '</pre>';
		}
		
		exit;
	} else if ($get->restore) {
		if (user_level(0)) {
			$id = (int) $get->pid;
			$sql->query("SELECT * FROM $admin->tbl WHERE $admin->pk = '$id'");
			$row = $sql->fetch();
			
			if ($row['data']) {
				$data = unserialize(gzuncompress($row['data']));
				
				if (is_array($data) && $row['source_table']) {
					if ($data['dependent_tables']) {
						$children = $data['dependent_tables'];
						unset($data['dependent_tables']);	
					}

					$sql->insert($row['source_table'], $sql->sanitize($data));
					
					if ($sql->insert_id()) {
						if ($children) {
							foreach ($children as $tbl=>$rows)
							{
								foreach ($rows as $r)
								{
									$sql->insert($tbl, $sql->sanitize($r));	
								}
							}
						}
						
						$sql->query("DELETE FROM $admin->tbl WHERE $admin->pk = '$id'");
						$func->reload("Item has been restored successfully");
					}
				}
			}
		}
	}
	
	
	if ($get->empty && user_level(0)) {
		$sql->query("TRUNCATE $admin->tbl");
		$func->log("Cleared all delete archive entries");
		$func->reload('Delete archive has been emptied');	
	}
	
	$admin->add_top_link('Clear Archive','icon-remove5', $conf->admin.'/'.$admin->manager.'?empty=1','control confirm', 'clear the delete archives');
	
	$admin->set_ordering('delete_dt DESC');
		
	
	$admin->add_search('delete_dt', 'Date', 'date');	
		
	
	$admin->add_field('source_table','Source Table', '200|left|sort');
	$admin->add_field('delete_dt','Date', '100|center|datetime|sort');
	
	$admin->add_link($conf->admin.'/'.$admin->manager, 'detail=1&pid=', 'icon-search5', 'Detail','popup');
	$admin->add_link($conf->admin.'/'.$admin->manager, 'restore=1&pid=', 'icon-reply2', 'Restore','control confirm', 'restore this item');
	
	$admin->action();
?>