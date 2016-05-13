<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.6";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>


<div class='mobile-fan-header'>
    <h1 class='page-title hite-blue'>Hite Jinro Dodgers Fan Sampling Event</h1>
    <div class='copy'>
        Once again, Hite is the official Korean beer of the Dodgers’ 2016 season.  
        Stop by our sampling tables at various stadium locations and receive your Hite Beer and Jinro Soju Cocktail discount coupons.  
        You can redeem at concessions where Hite Beer and Jinro Soju’s are sold. Must be 21 and over to receive discount coupons.
    </div>
</div>

<div class='fan-grid'>
	<div class='grid-item'><img src="<?php echo $http ?>/public/img/fan-1.jpg" alt=""></div>
    <div class='grid-item'><img src="<?php echo $http ?>/public/img/fan-2.jpg" alt=""></div>
    <div class='grid-item'><span class='toggle'>&plus;</span><img src="<?php echo $http ?>/public/img/fan-3.jpg" alt=""></div>
    <div class='grid-item'><img src="<?php echo $http ?>/public/img/fan-4.jpg" alt=""></div>
    <div class='grid-item text'>
    	<h1 class='page-title hite-blue'>Hite Jinro Dodgers Fan Sampling Event</h1>
        <div class='copy'>
            Once again, Hite is the official Korean beer of the Dodgers’ 2016 season.  
            Stop by our sampling tables at various stadium locations and receive your Hite Beer and Jinro Soju Cocktail discount coupons.  
            You can redeem at concessions where Hite Beer and Jinro Soju’s are sold. Must be 21 and over to receive discount coupons.
        </div>
	</div>
    <div class='grid-item special'><img src="<?php echo $http ?>/public/img/fan-5.jpg" alt=""></div>
    <div class='grid-item special'><img src="<?php echo $http ?>/public/img/fan-6.jpg" alt=""></div>
    <div class='grid-item special'><img src="<?php echo $http ?>/public/img/fan-7.jpg" alt=""></div>
    <div class='grid-item'><img src="<?php echo $http ?>/public/img/fan-8.jpg" alt=""></div>
    <div class='clear'></div>
</div>

<div class='page-content container'>
	<div class='lists-container'>
		<div class='list-container'>
			<div class="title-wrapper">
				<h2 class='section-title hite-blue'>Upcoming events</h2>
			</div>
            
            
            <ul class='schedule'>
            	<li class='header'>
                    <div class='date'>Date</div>
                    <div class='time'>Time</div>
                    <div class='game'>Game</div>
                    <div class='note'>Note</div>
                </li>
            
			<?php
				foreach ($events as $v)
				{
					echo "
					<li>
						<div class='date'>$v[date]</div>
						<div class='time'>$v[event_time]</div>
						<div class='game'>$v[game]</div>
						<div class='note'>$v[note]</div>
					</li>";
				}
			?>
            </ul>
		</div>
	</div>
    
    
</div>
<div class='page-content container'>
    <div class='fan-social-left'>
        <h2 class='section-title jinro-green'><a target="_blank"  href='<?php echo $social_links['instagram']['hite'] ?>'>FOLLOW US ON INSTAGRAM</a></h2>
        <ul id='instafeed'></ul>
    </div>
    <div class='fan-social-right'>
        <div class="fb-container">
            <h2 class='section-title hite-blue'><a target="_blank" href='<?php echo $social_links['facebook'] ?>'>Like us on Facebook</a></h2>
            <div class="fb-page" data-href="https://www.facebook.com/hitejinro" data-height='1165' data-tabs="timeline" data-small-header="true" data-adapt-container-width="true" data-hide-cover="true" data-show-facepile="true"><div class="fb-xfbml-parse-ignore"><blockquote cite="https://www.facebook.com/hitejinro"><a href="https://www.facebook.com/hitejinro">Hite Beer &amp; Jinro Soju</a></blockquote></div></div>
        </div>
	</div>
</div>

