



<ul>
<?php
	foreach ($events as $v)
	{
		echo "
		<li>
			<h2><a href='$http/$page/$v[alias]'>$v[name] - <span class='date'>$v[md]</span></a></h2>
			<p class='copy'>$v[content]</p>
		</li>";
		
	}


?>
</ul>