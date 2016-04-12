<div class='container'>
	<div class='newsletter-container'>
		<h2 class='title'>Get our newsletter</h2>
		<div class='field-row'>
			<input ref='email' type='email' placeholder='E-mail address' />
			<button class='btn-sub' type='submit'>Sign up</button>		
		</div>
	</div>
	<nav class='footer-links'>
		<ul>
			<?php
				foreach ($footer_navigation as $slug=>$link)
				{
					echo "<li>";
					echo "<a href='$link'>$slug</a>";
					echo "</li>";
				}
			?>		
		</ul>
	</nav>
	<div class='social-links'>
		<ul>
			<?php
				foreach ($social_links as $social=>$link)
				{
					echo "<li>";
					echo "<a href='$link'><span class='icon icon-$social'></span></a>";
					echo "</li>";
				}
			?>
		</ul>
	</div>
</div>
<div class='copyright'>
	<small>&copy; <?php echo date("Y") ?> HiteJinro.  All rights reserved.</small>
</div>