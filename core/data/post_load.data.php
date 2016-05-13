<?php	
	$msg = $func->get_msg();
	
	if ($msg) {
		$tpl->assign('system_msg', $msg, true);
	}
?>