<p>Select an item below to manage it's content</p>

<div class='big-links'>
<?php
	if ($admin_menu_raw) {
		foreach ($admin_menu_raw as $k=>$v)
		{
			echo "<span class='big-link'><a href='$admin_url/$k'><span class='icon $v[icon]'></span>$v[name]</a></span>";
		}
		
	}
?>
</div>

<div class='clear'></div>