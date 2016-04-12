<?php
	$webform = new Webform('contact');
	$webform->load();

	if ($_POST['action'] == 'submit-contact') {
		$result = $webform->submit($_POST);
		$tpl->assign('webform_js', $result['webform_js']);
		$func->set_msg($result['msg']);
	}

	$tpl->assign('form', $webform->get_post());			
	$tpl->set_template('content','webform-contact.tpl.php');
?>