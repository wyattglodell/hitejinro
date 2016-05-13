<?php
	$tpl->js('https://maps.google.com/maps/api/js');
	$tpl->js('store_locator.js');
	$tpl->js('handlebars.js');

	$tpl->assign('online', 'http://www.binnys.com/all/jinro');
	$tpl->set_template('content', 'where-to-buy.tpl.php');
?>