<?php
	if ($recipes) {
		foreach ($recipes as $v)
		{
			$odd = $odd ? '' : 'odd';
			
			echo "
				<div class='recipe $odd'>
					<div class='img-wrapper'>
						<img src='$v[image]'>
					</div>
					<div class='text-wrapper'>
						<h2 class='headline'>$v[name]</h2>
						<div class='copy'>$v[content]</div>
						<a href='/' class='btn-main'>Find ".ucfirst($site)."</a>
					</div>
				</div>
			";
		}
	}
?>