<?php
	$admin->set_keyword('Webform');
	$admin->set_tbl($conf->WEBFORM);
	$admin->set_primary_key('webform_id');
	
	if (user_access('Manage Webforms', 'Webforms')) {
		$admin->allow_delete(1);
		$admin->allow_add(1);
		$admin->allow_edit(1);
	}
		
	$admin->add_field('slug','ID', '50|center|sort|edit');
	$admin->add_field('name','Name', '50|center|sort');
	$admin->add_field('email','Email Address', '100|center|sort');
	$admin->add_field('subject','Subject', '100|center|sort');
	$admin->add_field('from_name','From Name', '100|center|sort');
	$admin->add_field('from_email','From Email', '100|center|sort');

	if (user_access('Manage Webforms', 'Webforms')) {
		$admin->add_toggle('captcha','Use Captcha');
		$admin->add_link($conf->admin.'/webform-fields/', 'pid=', 'icon-tools', 'Build Form','');
		$admin->add_link($conf->admin.'/webform-generate','pid=', 'icon-code', 'Generate Code','popup');
	}
	
	$admin->add_link($conf->admin.'/webform-submissions', 'action=csv&type=new&pid=', 'icon-download2', 'Download New','');
	$admin->add_link($conf->admin.'/webform-submissions', 'action=csv&pid=', 'icon-download2', 'Download All','');
	
	$admin->form_field('name','Name','text', array('final'=>true));
	$admin->form_field('email','Email','text',array('default_value'=>'default'));
	$admin->form_field('subject','Subject','text');
	$admin->form_field('from_name','From Name','text',array('default_value'=>'default'));
	$admin->form_field('from_email','From Email','text',array('default_value'=>'default'));
	$admin->form_field('slug','Slug','hidden',array('alias'=>'name'));
	
	if (user_access('Manage Webforms', 'Webforms')) {
		$admin->form_field('captcha','Use Captcha','toggle');
	}
	
	$admin->action();
?>