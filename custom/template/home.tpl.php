<?php if (!$site) { ?>
	<div id='choices-container'>
		<div class='choice'>
			<a href='?site=hite'><img src='<?php echo $http ?>/public/img/split_hite.png'></a>
		</div>
		<div class='choice'>
			<a href='?site=jinro'><img src='<?php echo $http ?>/public/img/split_jinro.png'></a>
		</div>
	</div>
	<div id='center-logo'>
		<img src='<?php echo $http ?>/public/img/logo_public.png' />
	</div>
<?php } else { ?>
	<section id='featured'>
		<?php
			foreach ($featured as $item)
			{
				echo "<div class='featured-item'>";
				echo "<a href='".($item['featuredLink'] ? $item['featuredLink'] : $item['link'])."'>";
				echo "<img src='$http/public/img/".$site."/$item[img]'>";
				echo "<div class='text-wrapper'>";
				echo "<h2 class='headline'>".($item['featuredTitle'] ? $item['featuredTitle'] : $item['headline'])."</h2>";
				echo "<p class='subtitle'>$item[subtitle]</p>";
				echo "</div>";
				echo "</a>";
				echo "</div>";
			}
		?>
	</section>
	<section id='brand-intro' class='container'>
		<div class='text-wrapper'>
			<img class='brand-logo' src="<?php echo $http ?>/public/img/logo_light_<?php echo $site ?>.png">
			<?php
				echo "<h2 class='headline'>$brand_intro[headline]</h2>";
				echo "<p class='copy'>$brand_intro[description]</p>";
			?>
			<a class='btn-main' href="<?php echo $http ?>/products">Learn more</a>		
		</div>
		<div class='img-wrapper'>
			<img class='brand-img' src="<?php echo $http ?>/public/img/<?php echo $site."/".$brand_intro['img'] ?>">
		</div>
	</section>
	<section id='instafeed-wrapper'>
		<h2 class='headline'><a href='<?php echo $social_links['instagram'][$site] ?>' target='_blank'><span>#<?php echo strtoupper($site) ?></span></a> on Instagram</h2>
		<div id='instafeed' data-site='<?php echo $site ?>' data-hash='<?php echo $instagram_hash ?>'></div>
	</section>
<?php } ?>