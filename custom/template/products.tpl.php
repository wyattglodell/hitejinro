<div class='product-top-content'>
	<?php echo $product_top_content ?>
</div>


<?php
	if ($products) {
		foreach ($products as $v)
		{
			$odd = $odd ? '' : 'odd';
			
			echo "
				<div class='product $odd'>
					<img src='$v[image]'>
					
					<h2>$v[name]</h2>
					<p>$v[content]</p>
				</div>
			";
		}
	}
?>