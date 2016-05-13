<?php
	$events = array();

	$sql->query("SELECT *, DATE_FORMAT(event_date, '%m/%d/%y') as `date` FROM $conf->FAN_PAGE WHERE status = 1 ORDER BY event_date");
	while ($row = $sql->fetch())
	{
		
		$row['content'] = $func->truncate($row['content']);
		$events[] = $row;	
	}
	
	
	$tpl->js('instafeed.min.js', '', true);
	$tpl->js('fan.js', '', true);		

	$tpl->assign('events', $events);
	$tpl->set_template('content', 'fan-page.tpl.php');
?>