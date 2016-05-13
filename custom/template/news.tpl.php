<?php /*<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.6";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>*/ ?>

<div class='page-hero'>
	<div class='title-wrapper'>
		<h1 class='page-title'>News &amp; Events</h1>
	</div>
</div>

<div class='page-content container'>
	<div class="lists-container">
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
							<span class='img-wrapper' target='$v[target]'><img src='$v[photo]'></span>
							<div class='text-wrapper'>
								<h3 class='headline'>$v[name]</h3>
								<div class='copy'>$v[content]</div>
						"; 
						if ($v['more']) {
							echo "<a class='btn-main' href='$v[more]' target='$v[target]'>Read More</a>";
						}
						
						echo "
								
							</div>
						</li>";
					}
				echo "</ul></div>";
			}
		?>
	</div>    <?php /*
	<div class="fb-container">
		<h2 class='section-title'>Like us on Facebook</h2>
		<div class="fb-page" data-href="https://www.facebook.com/hitejinro" data-tabs="timeline" data-small-header="true" data-adapt-container-width="true" data-hide-cover="true" data-show-facepile="true"><div class="fb-xfbml-parse-ignore"><blockquote cite="https://www.facebook.com/hitejinro"><a href="https://www.facebook.com/hitejinro">Hite Beer &amp; Jinro Soju</a></blockquote></div></div>
	</div> */ ?>
</div>