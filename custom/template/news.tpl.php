<div class='hero'>
	<div class='title-wrapper'>
		<h1 class='page-title'>News &amp; Events</h1>
	</div>
</div>

<div class="container lists-container">
<?php
	foreach ($news as $k=>$arr)
	{
		echo "
		<div class='list-container $k'>
			<h2 class='headline'>$k</h2>
			<ul class='list'>";
			foreach ($arr as $v)
			{
				echo "
				<li>
					<a href='$http/$page/$v[alias]' class='img-wrapper'><img src='$v[photo]'></a>
					<div class='text-wrapper'>
						<h3 class='headline'>$v[name]</h3>
						<div class='copy'>$v[content]</div>
						<a class='btn-main' href='$http/$page/$v[alias]'>Read More</a>
					</div>
				</li>";
			}
		echo "</ul></div>";
	}
?>
</div>