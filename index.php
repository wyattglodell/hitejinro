<?php
	$micro_start = explode(' ', microtime());

	require_once ('core/inc/config.php');
	$controller = new Controller;
	$controller->parse($_GET);
	$controller->run();

	$micro_end = explode(' ', microtime());
	
	$settings = Registry::get('setting');

	if (is_object($settings) && $settings->load_time == 'On') {
		echo "<p class='center bold system-load-time'>Load Time: ".number_format((($micro_end[1] + $micro_end[0]) - ($micro_start[1] + $micro_start[0])), 4)."s</p>";
	}	
?>