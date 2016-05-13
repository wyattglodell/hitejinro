<?php
	$admin->set_keyword('Log');
	$admin->set_tbl($conf->LOG);
	$admin->set_primary_key('log_id');

	$admin->allow_add(0);
	$admin->allow_edit(0);
	
	if ($get->search) {
		$tpl->set_template('base', 'admin/log-search.tpl.php');
		return;
	}
	
	if ($get->detail) {
		if (user_level(0)) {
			$id = (int) $get->pid;
			$sql->query("SELECT * FROM $admin->tbl WHERE $admin->pk = '$id'");
			$log = $sql->fetch();
			
			if ($log['data']) {
				$data = unserialize(gzuncompress($log['data']));
			}
			echo '<pre>';
			echo "URI: ".$log['uri']."\n\n";
			
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
	}
	
	
	if (user_level(0)) {
		if ($get->empty) {
			$sql->query("TRUNCATE $admin->tbl");
			$func->log("Cleared all log entries");
			$func->reload('Log has been emptied');	
		}
		
		$admin->allow_delete(1);	
	}
	
	$admin->add_top_link('Clear Log','icon-remove5', $conf->admin.'/'.$admin->manager.'?empty=1','control confirm', 'clear the logs');
	$admin->add_top_link('Search Detail','icon-search', $conf->admin.'/'.$admin->manager.'?search=1','popup');
	
	$admin->set_ordering('time DESC');
	
	function severity($data)
	{
		if ($data['severity'] == 'severe' || $data['severity'] == 'warning') {
			return "<span class='icon icon-warning warning'></span>";
		} else if ($data['severity'] == 'notice') {
			return "<span class='icon icon-warning notice'></span>";
		} 
	}
	
	$sql->query("SELECT DISTINCT severity FROM $admin->tbl");
	while ($row = $sql->fetch())
	{
		if ($row['severity']) {
			$severity[$row['severity']] = $row['severity'];	
		}
	}
	
	if ($_POST['detail_log_search']) {
		$value = $sql->sanitize($_POST['detail_search']);
		$start = $func->date_to_sql($_POST['start_date']);
		$end = $func->date_to_sql($_POST['end_date']);
		
		$rows = array();
		
		$sql->query("SELECT * FROM $admin->tbl WHERE time >= '$start 00:00:00' AND time <= '$end 23:59:59' ORDER BY time DESC");
		while ($row = $sql->fetch())
		{
			if (strpos($row['uri'], $value) !== false) {
				$rows[] = $row;	
			} else if ($row['data']) { 
				$result = false;
				$data = unserialize(gzuncompress($row['data']));
				
				if (is_array($data)) {
					foreach ($data as $k=>$v)
					{
						if (is_array($v)) {
							foreach ($v as $key=>$val)
							{
								if (strtolower($val) === strtolower($value)) {
									$result = true;	break 2;
								}
							}
						} else {
							if (strtolower($v) === strtolower($value)) {
								$result = true;	break;
							}
						}
					}
				} else {
					$result = strpos($data, $value);
				}
				
				if ($result !== false) {
					$rows[] = $row;	
				}
			} 
		}
		
		if ($rows) {
			$admin->set_data($rows);	
		} else {
			$func->set_msg('No records found');	
		}
	}
	
	$admin->add_search('severity', 'Warning', 'select', $severity);	
	$admin->add_search('time', 'Date', 'date');	
	$admin->add_search('message', 'Message', 'text');	
	$admin->add_search('username', 'Username', 'text');	
	$admin->add_search('ip', 'IP', 'text');	
		
	$admin->add_field('severe','Warning', '20|center|sort|html|callback=severity');
	
	$admin->add_field('time','Date', '100|center|datetime|sort');
	$admin->add_field('message','Message', '500');
	$admin->add_field('username','Username', '100|left|sort');
	$admin->add_field('ip','IP', '100|center|sort');
	
	$admin->add_link($conf->admin.'/'.$admin->manager, 'detail=1&pid=', 'icon-search5', 'Detail','popup');
	$admin->action();
?>