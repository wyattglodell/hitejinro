<?php
	if (user_access('Manage Webforms', 'Webforms')) {
		$sql->query("SELECT slug FROM $conf->WEBFORM WHERE webform_id = ".intval($admin->pid));
		$form = $sql->fetch();
		
		$wf = new Webform($form['slug']);
		
		$html = $wf->get_tpl();
		$code = $wf->get_data();
		
		if (!file_exists($conf->custom.'/data/'.$form['slug'].'.data.php') && !file_exists($conf->custom.'/template/webform-'.$form['slug'].'.tpl.php')) {
			file_put_contents($conf->custom.'/data/'.$form['slug'].'.data.php', $code);
			file_put_contents($conf->custom.'/template/webform-'.$form['slug'].'.tpl.php', $html);
			
			chmod($conf->custom.'/data/'.$form['slug'].'.data.php', 0777);
			chmod($conf->custom.'/template/webform-'.$form['slug'].'.tpl.php', 0777);
			
			echo "<p><center>".$form['slug'].'.data.php and webform-'.$form['slug'].'.tpl.php have been generated</center></p>';
		} else {
			echo "<p><center>".$form['slug'].'.data.php or webform-'.$form['slug'].'.tpl.php already exists</center></p>';
		}
	}
	exit;
?>