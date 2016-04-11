<?php if (!$site) { ?>
	<div id='choices-container'>
		<div>
			<a href='?site=hite'><img src='/public/img/split_hite.png'></a>
		</div>
		<div>
			<a href='?site=jinro'><img src='/public/img/split_jinro.png'></a>
		</div>
	</div>
	<div id='center-logo'>
		<img src='/public/img/logo_color.png' />
	</div>
<?php } else { ?>
	<section id='featured'>
		<?php
			foreach ($featured as $item)
			{
				echo "<div class='featured-item'>";
				echo "<a href='$item[link]'>";
				echo "<img src='/public/img/".$site."/$item[img]'>";
				echo "<div class='text-wrapper'>";
				echo "<h2 class='headline'>$item[headline]</h2>";
				echo "<p class='subtitle'>$item[subtitle]</p>";
				echo "</div>";
				echo "</a>";
				echo "</div>";
			}
		?>
	</section>
	<section id='brand-intro' class='container'>
		<div class='text-wrapper'>
			<img class='brand-logo' src="/public/img/logo_light_<?php echo $site ?>.png">
			<?php
				echo "<h2 class='headline'>$brand_intro[headline]</h2>";
				echo "<p class='body-text'>$brand_intro[description]</p>";
			?>
			<a class='btn-sub' href="/products">Learn more</a>		
		</div>
		<div class='img-wrapper'>
			<img class='brand-img' src="/public/img/<?php echo $site."/".$brand_intro[img] ?>">
		</div>
	</section>
	<section id='instagram'>
		<h2 class='headline'><span>#<?php echo $site ?></span> on Instagram</h2>
		<div id='instagram-feed'>
			
		</div>
	</section>
<?php } ?>