<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.6";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<div class='page-hero'>
	<div class='title-wrapper'>
		<h1 class='page-title'>News &amp; Events</h1>
	</div>
</div>

<div class='container lists-container'>
<?php
	foreach ($news as $k=>$arr)
	{
		echo "
		<div class='list-container $k'>
			<h2 class='section-title'>$k</h2>
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

<div class="container">
	<div class="fb-page" data-href="https://www.facebook.com/hitejinro" data-tabs="timeline" data-small-header="true" data-adapt-container-width="true" data-hide-cover="true" data-show-facepile="true"><div class="fb-xfbml-parse-ignore"><blockquote cite="https://www.facebook.com/hitejinro"><a href="https://www.facebook.com/hitejinro">Hite Beer &amp; Jinro Soju</a></blockquote></div></div>
</div>