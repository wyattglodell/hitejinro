<?php if ($product_top_content) { ?>
<div class='product-top-content'>
	<div class="container">
		<?php echo $product_top_content ?>
	</div>
</div>
<?php } ?>

<?php
	if ($products) {
		foreach ($products as $v)
		{
			$odd = $odd ? '' : 'odd';
			
			echo "
				<div class='product $odd'>
					<div class='img-wrapper'>
						<img src='$http$v[image]'>
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