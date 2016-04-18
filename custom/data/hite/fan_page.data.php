<?php
	$admin->set_keyword('Fan Page Event');
	$admin->set_tbl($conf->FAN_PAGE);
	$admin->set_primary_key('fan_page_id');

	$admin->allow_add(1);
	$admin->allow_edit(1);
	$admin->allow_delete(1);	
	
	$admin->set_ordering('event_date ASC');

	#$admin->set_description('');
	
	/*
	# this function allows you to hook into the rows that will be displayed
	function view_data($data)
	{
		
		return $data;	
	}
	
	# this function is run before form data is processed. It can also override form populated for add/edit
	function pre_process($data)
	{
		$admin = Registry::get('admin');
		$func = Registry::get('func');
		
		if ($admin->action == 'submit_add' || $admin->action == 'submit_edit') {
			$data['token'] = md5(rand());
		}
		
		return $data; # if not a submit, make sure nothing is returned
	}
	
	# this function is run after form data is processed
	function post_process($data, $msg, $warning)
	{
		$admin = Registry::get('admin');
		$func = Registry::get('func');
		
		if ($warning == 'notice') {
			$msg = 'New Message';	
		}
		
		return array($msg, $warning); #set $msg to empty to prevent logging
	}
	*/
	
	/*
	$admin->error(true); # show if table is missing
	
	$admin->add_dependency($conf->CHILD_TABLE); # dependency tables will also delete rows with the same primary_key
	$admin->add_dependency($conf->ANOTHER_CHILD_TABLE);
	$admin->query_filter('is_content = 1');	
		
	$admin->thumbnail('example','aspect',200,0); # thumbnail_name, aspect | fixed | crop, width, height. for aspect: one side is optional, pass 0
	$admin->set_parent('administrator:webform', 'name', $conf->WEBFORM, 'webform_id');	
	$admin->add_control('Delete', $admin->page, 'action=delete', 'icon-delete2', true); 
	$admin->add_top_link('Clear Log','icon-remove7', $conf->admin.'/administrator:log?empty=1','ajax control confirm', 'clear the log');
	$admin->paginate(100); # set to 0 to disable pagination
	
	*/
	
	
	#$admin->add_field('weight','Weight', '50|center|sort');
	$admin->add_field('event_date','Date', '50|center|date');
	
	
	$admin->add_field('name','Name', '200|left|sort|edit');
	
	$admin->add_toggle('status','Status');
	#$admin->add_toggle('status','Status','icon-circle2','icon-circle2', false);
	#$admin->add_toggle('status','Status','icon-checkbox','icon-checkbox-unchecked2', true);
	
	#$admin->add_link($conf->admin.'/quiz', 'pid=', 'icon-list2', 'Quiz','popup'); # pass filter=page_id:2 to populate filtering, type-redirect|type-blank|type-delete|type-hide|type-toggle
	
	$admin->form_field('name','Name','text','required');
	$admin->form_field('alias','Alias','hidden', 'alias:name');
	$admin->form_field('event_date','Date','date', 'required');
	$admin->form_field('content','Content','editor', 'required');
	#$admin->form_field('weight','Order','weight');
	$admin->form_field('status','Status','toggle', array('default_value' => true));
	
	#$admin->form_field('images','Images','file', "group:Photos|table:$conf->PRODUCT_IMAGE|repeat:1");
	#$admin->form_field('caption','Caption','text', "group:Photos|table:$conf->PRODUCT_IMAGE|repeat:1");

	#$admin->add_search('time', 'Date', 'date');	
	#$admin->add_search('page_id', 'Page', 'select', $pages);		
		
	#$admin->set_update_tpl('base','admin/form-builder.tpl.php');
	#$admin->set_data($view_data); # use this to manually set the data being listed
	
	$admin->action();
?>