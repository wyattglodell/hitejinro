<?php
	$sites = Site::$sites;
	$site = Site::get_current_site();
	if ($site) $navigation = $site_data['content'][$site]['pages'];
?>

<div id='site-toggle-bar' class="<?php echo $site ?>">
	<?php
		foreach ($sites as $v)
		{
			echo "<a href='?site=$v' class='site-toggle".($v === $site ? " active" : "")."'><span class='$v'></span></a>";
		}
	?>
</div>
<div class='container ctrl-links'>
	<a id="menu-toggle"><span></span></a>
	<div class='logo-wrapper'>
		<a class='site-logo' href="?site=<?php echo $site ?>">
			<img src="/public/img/logo_<?php echo $site ?>.png"
				 srcset="/public/img/logo_<?php echo $site ?>.png 768w,
				 		/public/img/logo_sm_<?php echo $site ?>.png 310w">
		</a>
	</div>
</div>
<div id='links-panel' class='container'>
	<nav class='nav-links'>
		<a class='site-logo' href="?site=<?php echo $site ?>">
			<img src="/public/img/logo_<?php echo $site ?>.png"
				 srcset="/public/img/logo_<?php echo $site ?>.png 768w,
				 		/public/img/logo_sm_<?php echo $site ?>.png 310w">
		</a>
		<ul>
			<?php
				foreach ($navigation as $nav)
				{
					echo "<li>";
					echo "<a href='$nav[link]'>$nav[headline]</a>";
					echo "</li>";
				}
			?>
		</ul>
	</nav>
	<div class='social-links'>
		<ul>
			<?php
				foreach ($site_data['theme']['global']['social'] as $social=>$link)
				{
					echo "<li>";
					echo "<a href='$link'><span class='icon icon-$social'></span></a>";
					echo "</li>";
				}
			?>
		</ul>
	</div>
</div>