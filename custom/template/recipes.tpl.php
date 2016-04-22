<?php
	if ($recipes) {
		foreach ($recipes as $k=>$v)
		{
			$odd = $odd ? '' : 'odd';
			
			echo "
				<div class='recipe $odd'>
					<div class='text-wrapper'>
						<h2 class='headline'>$v[name]</h2>
						<div class='copy'>$v[content]</div>
						<a href='/' class='btn-main'>Find ".ucfirst($site)."</a>
					</div>
					<div class='img-wrapper'>
						<img src='$v[image]'>
					</div>
				</div>
			";
		}
		if ($k % 3 == 1) {
			echo "
				<div class='recipe $odd'>
					<div class='text-wrapper'>
						<h2 class='headline'></h2>
						<div class='copy'></div>
						<a href='/' class='btn-main'></a>
					</div>
					<div class='img-wrapper'>
					</div>
				</div>
			";
		} else if ($k % 3 == 0) {
			echo "
				<div class='recipe $odd'>
					<div class='text-wrapper'>
						<h2 class='headline'></h2>
						<div class='copy'></div>
						<a href='/' class='btn-main'></a>
					</div>
					<div class='img-wrapper'>
					</div>
				</div>
				<div class='recipe $odd'>
					<div class='text-wrapper'>
						<h2 class='headline'></h2>
						<div class='copy'></div>
						<a href='/' class='btn-main'></a>
					</div>
					<div class='img-wrapper'>
					</div>
				</div>
			";
		}
	}
?>