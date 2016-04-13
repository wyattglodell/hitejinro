<?php
	$sql->query("SELECT * FROM $conf->RECIPE WHERE status = 1 ORDER BY weight");
	while ($row = $sql->fetch())
	{
		$row['image'] = $func->get_img($row['image'], 'small');
		$recipes[] = $row;
	}
	
	$tpl->assign('recipes', $recipes);

	$tpl->set_template('content', 'recipes.tpl.php');
?>