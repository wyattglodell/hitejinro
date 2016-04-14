<?php
	$webform = new Webform('mailinglist');
	$webform->load();

	if ($_POST['action'] == 'submit-mailinglist') {
		$result = $webform->submit($_POST);
		$tpl->assign('webform_js', $result['webform_js']);
		$func->set_msg($result['msg']);
	}

	$tpl->assign('form', $webform->get_post());			
	$tpl->set_template('content','webform-mailinglist.tpl.php');
?>