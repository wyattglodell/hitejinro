<div class='page-hero'>
	<div class='title-wrapper'>
		<h1 class='page-title'>Hite Fan Page</h1>
		<p class='copy'>Once again, Hite will be the official Korean beer for the Dodgers for 2016 season.  Stay tuned for upcoming events at Dodgers!</p>
	</div>
</div>

<div class='page-content container'>
	<div class='lists-container'>
		<div class='list-container'>
			<div class="title-wrapper">
				<h2 class='section-title'>Upcoming events</h2>
				<p class='copy'>Stop by and get your coupon at the sampling event activation area</p>
			</div>
			<ul class='list'>
			<?php
				foreach ($events as $v)
				{
					echo "
					<li>
						<h3 class='headline'><a href='$http/$page/$v[alias]'>$v[name] - <span class='date'>$v[md]</span></a></h3>
						<div class='text-wrapper'>
							<div class='copy'>$v[content]</div>
							<a class='btn-main' href='$http/$page/$v[alias]'>Read More</a>
						</div>
					</li>";
					
				}
			?>
			</ul>		
		</div>
	</div>
	<div id='fans-image'>
		<img src="/public/img/hite/fans1.jpg" alt="">
		<img src="/public/img/hite/fans2.jpg" alt="">
		<img src="/public/img/hite/fans3.jpg" alt="">
		<img src="/public/img/hite/fans4.jpg" alt="">
	</div>
</div>