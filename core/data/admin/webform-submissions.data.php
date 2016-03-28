<?php
	$admin->set_keyword('Webform Submission');
	$admin->set_tbl($conf->WEBFORM_SUBMISSION);
	$admin->set_primary_key('submission_id');
	
	$admin->allow_add(0);
	$admin->allow_edit(0);
	$admin->allow_delete(1);
		
	$admin->set_parent('administrator:webform', 'name', $conf->WEBFORM, 'webform_id');

	$admin->add_top_link('Download CSV','download.gif', $func->href($admin->page(), 'action=csv'),'');
	
	$data=array();
	
	$webform_id = $admin->pid;
	
	$sql->query("SELECT *,t.submission_id FROM $admin->tbl t LEFT JOIN $conf->WEBFORM_SUBMISSION_FIELD w ON t.submission_id = w.submission_id WHERE t.webform_id = $webform_id ORDER BY t.submission_date DESC");
	while ($row = $sql->fetch())
	{ 
		if (!$data[$row['submission_id']]['ip']) $data[$row['submission_id']]['ip'] = $row['ip'];
		if (!$data[$row['submission_id']]['submission_date']) $data[$row['submission_id']]['submission_date'] = $row['submission_date'];
		$data[$row['submission_id']][$admin->pk] = $row[$admin->pk];
		$data[$row['submission_id']][$row['name']] = $row['value'];
		$fields[] = $row['name'];
	}
		
	$admin->add_field('submission_date','Date', '100|center|sort');
	$admin->add_field('ip','IP', '75|center|sort');
	if ($fields) {
		foreach ($fields as $v)
		{
			$admin->add_field($v, $func->clean_name($v), '50|center|sort');
		}
	}
		
	$admin->form_field('label','Title','text');
	$admin->form_field('content','Content','editor');
	$admin->form_field('weight','Weight','weight');
	$admin->form_field('status','Status','toggle');
		
	if ($_POST['form_submitted']) {
		$ignore = $admin->default_ignore();
		
		foreach ($_POST as $k=>$v)
		{
			if (!in_array($k, $ignore)) {	
				switch ($k)
				{
					#case 'label' : $processed['slug'] = $func->slug($v, $tbl, 'slug', $primary_key, $_POST['edit']); break;
				}
				$processed[$k] = $sql->sanitize($v);
			}
		}
	}
	
	if ($admin->action == 'csv') {
		$func->download_csv('form-submission.csv', $data);
	} else if ($admin->action == 'edit' || $admin->action == 'add') {	
		$admin->form();
	} else if ($admin->action == 'toggle') {
		$admin->toggle();
	} else if ($admin->action == 'submit_add') {
		$admin->insert($processed);
	} else if ($admin->action == 'submit_edit') {
		$admin->update($processed);
	} else if ($admin->action == 'delete') {
		$admin->delete();
		$admin->delete_dependants($conf->WEBFORM_SUBMISSION_FIELD);				
	} else if ($admin->action == 'reslug') {
		$msg = $admin->reslug('name','slug');
	} else {
		$admin->set_data($data);	
		$admin->load_data();		
	}	
?>