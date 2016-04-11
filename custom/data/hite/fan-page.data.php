<?php

	$sql->query("SELECT *, DATE_FORMAT(event_date, '%b %e') as md FROM $conf->FAN_PAGE WHERE status = 1 ORDER BY event_date DESC LIMIT 5");
	while ($row = $sql->fetch())
	{
		$events[] = $row;	
	}

	$tpl->assign('events', $events);

	$tpl->set_template('content', 'fan-page.tpl.php');
?>