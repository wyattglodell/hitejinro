<?php
	$events = array();

	if ($get->b) {
		$sql->query("SELECT *, DATE_FORMAT(event_date, '%b %e') as md FROM $conf->FAN_PAGE WHERE status = 1 AND alias = '".$sql->sanitize($get->b)."'");

		$events = $sql->fetch();
		
		if ($events) {
			$events['photo'] = $func->get_img($events['photo'], 'small');
			
			$tpl->assign('events', $events);
			$tpl->set_template('content', 'fan-page-detail.tpl.php');
		} else {
			$func->error404();
		}
	} else {
		$sql->query("SELECT *, DATE_FORMAT(event_date, '%b %e') as md FROM $conf->FAN_PAGE WHERE status = 1 ORDER BY event_date DESC LIMIT 5");
		while ($row = $sql->fetch())
		{
			$row['content'] = $func->truncate($row['content']);
			$events[] = $row;	
		}

		$tpl->assign('events', $events);
		$tpl->set_template('content', 'fan-page.tpl.php');
	}
?>