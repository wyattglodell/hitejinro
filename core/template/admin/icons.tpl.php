<p><?php echo $current?></p>
<style type='text/css'>
	span.ico { font-size: 28px; }
</style>

<?php
	if ($icons) {
		echo "<table width='900' cellspacing='1' cellpadding='3'><tr>";
		
		$count = 0;
		foreach ($icons as $icon)
		{
			echo "<td><span class='ico icon-$icon'></span></td>";
			if ($count % 25 == 24) {
				echo "</tr><tr>";	
			}
			$count++;
		}
		echo '</tr></table>';
	}
?>
<div class='br2'></div>
<a class='action' href='<?php echo $url?>?page=<?php echo $prev ?>'>Previous</a>
&nbsp; &nbsp; 
<a class='action' href='<?php echo $url?>?page=<?php echo $next ?>'>Next</a>
