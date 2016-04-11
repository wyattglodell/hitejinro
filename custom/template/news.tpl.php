



<ul>
<?php
	foreach ($news as $v)
	{
		echo "
		<li>
			<a href='$http/$page/$v[alias]' class='img'><img src='$v[photo]'></a>
			<h2><a href='$http/$page/$v[alias]'>$v[name]</a></h2>
			<p class='copy'>$v[content]</p>
		</li>";
		
	}


?>
</ul>