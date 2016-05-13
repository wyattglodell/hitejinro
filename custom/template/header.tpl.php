<div id='site-toggle-bar' class="<?php echo $current_site ?>">
	<?php /*<a href='/fan-page' class='site-toggle fan-page'>Hite Jinro Fan Page</a>*/ ?>
	<?php
		foreach ($sites as $v)
		{
			echo "<a href='?site=$v' class='site-toggle".($v === $current_site ? " active" : "")."'><span class='$v'></span></a>";
		}
	?>
    
    
</div>
<div class='container ctrl-links'>
	<a id="menu-toggle"><span></span></a>
	<div class='logo-wrapper'>
		<a class='site-logo mobile-logo' href="?site=<?php echo $current_site ?>">
			<img src="<?php echo $http ?>/public/img/logo_<?php echo $current_site ?>.png"
				 srcset="<?php echo $http ?>/public/img/logo_<?php echo $current_site ?>.png 768w,
				 		/public/img/logo_sm_<?php echo $current_site ?>.png 310w">
		</a>
	</div>
</div>
<div id='links-panel' class='container'>
	<nav class='nav-links'>
		<a class='site-logo' href="?site=<?php echo $current_site ?>">
			<img src="<?php echo $http ?>/public/img/logo_<?php echo $current_site ?>.png">
		</a>
		<ul>
			<?php
				foreach ($navigation as $nav)
				{
					$class= ($page == substr($nav['link'], 1)) ? "class='active'": "";
					echo "<li $class>";
					echo "<a href='$http$nav[link]'>$nav[headline]</a>";
					echo "</li>";
				}
			?>
		</ul>
	</nav>
	<div class='social-links'>
		<ul>
			<?php
				foreach ($social_links as $social=>$v)
				{
					if ($social == 'instagram') {
						$link = $v[$site];
					} else {
						$link = $v;
					}
					$popup = substr($link, 0, 4) == 'http' ? "_blank" : '_self';
					if ($popup == '_self') $link = $http.$link;
					
					echo "<li>";
					echo "<a href='$link' target='$popup'><span class='icon icon-$social'></span></a>";
					echo "</li>";
				}
			?>
		</ul>
	</div>
</div>