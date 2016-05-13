<?php
	if ($recipes) {
		foreach ($recipes as $k=>$v)
		{
			#$odd = $odd ? '' : 'odd';
			#$special = $k == 1 ? "filler" : '';
			
			echo "
				<div class='recipe item-$k'>
					<div class='text-wrapper $special'>
						<h2 class='headline' style='  background: #$v[hex]; 
						  background: -webkit-linear-gradient(left,rgba($v[dec]1),rgba($v[dec]0)); 
						  background: -o-linear-gradient(right,rgba($v[dec]1),rgba($v[dec]0));
						  background: -moz-linear-gradient(right,rgba($v[dec]1),rgba($v[dec]0)); 
						  background: linear-gradient(to right, rgba($v[dec]1), rgba($v[dec]0));'>
						  
						  $v[name]
						</h2>
						<div class='copy'>$v[content]</div>
						<a href='/' class='btn-main'>Find ".ucfirst($site)."</a>
					</div>
				";
				if ($v['image']) {
					echo "
					<div class='img-wrapper'>
						<img src='$v[image]'>
					</div>";
				}
				echo "
				</div>
			";
		}
		if ($k % 3 == 1) {
			echo "
				<div class='recipe '>
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