<?php
	$admin->set_keyword('Menu');
	$admin->set_tbl($conf->PAGE);
	$admin->set_primary_key('page_id');
	
	$admin->allow_add(1);
	$admin->allow_edit(1);
	$admin->allow_delete(1);
	
	$admin->set_ordering('weight');
		
	$menu = new Menu;
	$menu->update_tree();
	$menu->load_tree(true);
	$view_data = $menu->get_tree();
	$list = $menu->get_admin_tree();
		
	function custom_display($data)
	{
		return str_repeat('<span class="indent"></span>', $data['counter']).$data['name'];
	}
	
	$admin->add_field('weight','Weight', '50|center');
	$admin->add_field('name','Name', '200|html|edit|callback=custom_display');
	$admin->add_field('url','URL', '200|center');
	$admin->add_toggle('menu','Menu','icon-circle2');
	$admin->add_toggle('is_content','Content','icon-circle2');
	$admin->add_toggle('attachable','Attachable','icon-circle2');

	$admin->form_field('parent_id','Parent','select', array('list'=>$list,'required'=>false, 'ignore_self' => true));
	$admin->form_field('status','Status','hidden', array('default_value' => 1));
	
	
	
	$admin->form_field('name','Name','text', array('required'=>true,'alias'=>'alias'));
	
	if (user_level(0)) {
		$admin->form_field('alias','Alias','text');
 	}
	$admin->form_field('menu_title', 'Menu Title', 'text', array('clone'=>'name'));
	$admin->form_field('title', 'Page Title', 'text', array('clone'=>'name'));
	
	$admin->form_field('url','URL','text');
	$admin->form_field('menu','Menu','toggle');
	$admin->form_field('is_content','Content','toggle');
	$admin->form_field('attachable','Attachable','toggle');
	$admin->form_field('weight','Weight','weight');
	
	function pre_process($data)
	{
		$admin = Registry::get('admin');
		
		$data['modified'] = date('Y-m-d H:i:s');
		
		if ($admin->action == 'submit_add') {
			$data['author']	= User::get_username();
		}
		
		return $data;
	}
	
	$admin->set_data($view_data);
	
	$admin->action();
?>