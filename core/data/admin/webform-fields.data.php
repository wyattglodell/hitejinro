<?php	
	$admin->set_keyword('Webform Field');
	$admin->set_tbl($conf->WEBFORM_FIELD);
	$admin->set_primary_key('field_id');
	
	$admin->allow_add(1);
	$admin->allow_edit(1);
	$admin->allow_delete(1);
	
	$admin->set_ordering('weight');
		
	$admin->set_parent('administrator:webforms', 'name', $conf->WEBFORM, 'webform_id');
			
	$admin->add_field('weight','Weight', '50|center|sort');
	$admin->add_field('name','Name', '100|center|sort');
	$admin->add_field('label','Label', '200|center|sort');
	$admin->add_field('type','Type', '100|center|sort');
	$admin->add_field('subtype','Subtype', '100|center|sort');
	$admin->add_field('options','Options', '200|center|sort');
	$admin->add_toggle('required','Required', 'checked.gif','unchecked.gif');
	$admin->add_toggle('unique','Unique', 'checked.gif','unchecked.gif');
	
	$admin->form_field('weight', 'Weight', 'hidden');
	$admin->form_field('name', 'Name', 'hidden');
	$admin->form_field('label', 'Weight', 'hidden');
	$admin->form_field('type', 'Weight', 'hidden');
	$admin->form_field('subtype', 'Weight', 'hidden');
	$admin->form_field('options', 'Weight', 'hidden');
	$admin->form_field('required', 'Weight', 'hidden');
	$admin->form_field('unique', 'Weight', 'hidden');

	$tpl->assign($admin->parent_id, $admin->pid);
	$tpl->assign('new_weight',$admin->get_next_weight());
	$tpl->assign('admin_page', $admin->page());
		
	$admin->set_update_tpl('admin/webform-fields.tpl.php');
	
	function pre_process($data)
	{

		if ($data['options'] == 'custom') {
			$data['options'] = $data['custom_options'];
		}

		return $data;	
	}
	
	$admin->action();
?>