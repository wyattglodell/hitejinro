<?php

	$site = Site::get_current_site();

	$sql->query("SELECT * FROM $conf->PRODUCT WHERE status = 1 AND site = '$site' ORDER BY weight");
	while ($row = $sql->fetch())
	{
		$row['image'] = $func->get_img($row['image'], 'small');
		$products[] = $row;
	}
	
	$tpl->assign('products', $products);
	
	
	$content = $func->get_content($site.'-product');

	$tpl->assign('product_top_content', $content['content']);

	$tpl->set_template('content', 'products.tpl.php');
?>