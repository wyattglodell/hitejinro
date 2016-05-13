<?php
	$admin->set_keyword('Page');
	$admin->set_tbl($conf->PAGE);
	$admin->set_primary_key('page_id');
		
	$admin->allow_add(1);
	$admin->allow_delete(1);	
	$admin->allow_edit(1);
	
	$menu = new Menu;
	
	$admin->set_ordering('modified DESC');
		
	function custom_field($type, $name, $label, $data, $option)
	{
		$get = Registry::get('get');
		$admin = Registry::get('admin');
	
		if ($admin->action == 'add' || !$data['menu'] && $data['parent_id']) {
			$value = $data[$name] ? $data[$name] : $get->parent_id;
			
			$html = "<select name='$name' class='select  $required'>";
			$html .= "<option value='' class='option'> -- Please Select -- </option>";
			if ($option['list']) {
				foreach ($option['list'] as $k=>$v)
				{
					$sel = $value == $k ? "selected='selected'" : '';
					$html .= "<option value='$k' $sel>$v</option>";
				}
			}
			$html .= "</select>";	
		}
		
		return $html;	
	}
	
	$admin->add_field('weight','Weight', '50|center|sort');
	$admin->add_field('name','Name', '300|left|sort|edit');
	$admin->add_field('title','Title', '300|left|sort');
	#$admin->add_field('full_alias','URL', '300|left|sort');
	
	$admin->add_field('modified','Modified', 'center|datetime|120');
	$admin->add_field('author','Author', '100|center|sort');
	$admin->add_toggle('status','Status','icon-circle2');
	$admin->add_toggle('menu','Menu','icon-circle2');
		
	if (user_level(0)) {
		$admin->form_field('name','Name','text', array('required'=>true));
		$admin->form_field('alias','Alias','text', array('alias'=>'name'));
 	} else {
		$admin->form_field('name','Name','text', array('final' => true));	
	}
	
	$admin->form_field('menu_title', 'Menu Title', 'text', array('clone'=>'name'));
	$admin->form_field('title', 'Page Title', 'text', array('clone'=>'name'));
	
	$admin->form_field('keyword', 'Meta Keywords', 'text');
	$admin->form_field('description', 'Meta Description', 'textarea', array('height'=>50));
	$admin->form_field('content','Content','editor');
	$admin->form_field('weight','Weight','weight');
	$admin->form_field('status','Status','toggle');
	
	$admin->form_field('is_content','Is Content','hidden', array('default_value' => 1));
	
	$admin->form_field('parent_id','Parent','hidden');
	
	$admin->query_filter('is_content = 1');
	
	
	function pre_process($data)
	{
		$admin = Registry::get('admin');
		
		if ($admin->action == 'submit_add') {
			$data['modified'] = date('Y-m-d H:i:s');
			$data['author']	= User::get_username();
		} else if ($admin->action == 'submit_edit') {
			$data['modified'] = date('Y-m-d H:i:s');
		}
		
		return $data;
	}

	$admin->action();
?>