<?php
	$count = 0;
	$sql->query("SELECT * FROM $conf->RECIPE WHERE status = 1 ORDER BY weight");
	while ($row = $sql->fetch())
	{
		
		$row['hex'] = $row['titlebar_color'] ? str_replace('#','', $row['titlebar_color']) : '60bb46';
		$row['name'] = strtolower($row['name']);
		
		$row['name'] = substr($row['name'],0, 5) == 'jinro' ? str_replace('jinro', '<span>jinro</span>', $row['name']) : '<span>jinro</span> '.$row['name'];
		 
		
		foreach (str_split($row['hex'], 2) as $v)
		{
			$row['dec'] .= hexdec($v).',';	
		}
		
		$row['image'] = $func->get_img($row['image'], 'small');
		$recipes[] = $row;
		
		$count++;
		
		if (false && $count == 1) {
			$recipes[] = array('name'=>"
				Serving up 21 Soju Cocktail Recipes, just for you! #jinrosojucocktail
			");
		}
	}
	
	$tpl->assign('recipes', $recipes);

	$tpl->set_template('content', 'recipes.tpl.php');
?>