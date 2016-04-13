<?php
	if ($recipes) {
		foreach ($recipes as $v)
		{
			$odd = $odd ? '' : 'odd';
			
			echo "
				<div class='recipes $odd'>
					<img src='$v[image]'>
					
					<h2>$v[name]</h2>
					<p>$v[content]</p>
				</div>
			";
		}
	}
?>