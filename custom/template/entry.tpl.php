<div class='container'>
	<h1><img src='/public/img/logo_white.png' alt='logo' /></h1>
	<h2 class='headline'>We need to check your age</h2>
	
	<form method='post' action='<?php echo $url ?>'>
		<input type='hidden' name='action' value='verify'>
		<input type='hidden' name='age' value=''>
		<div class='field-row'>
			<input type='number' id='verify-month' maxlength='2' placeholder='MM'>
			<input type='number' id='verify-day' maxlength='2' placeholder='DD'>
			<input type='number' id='verify-year' maxlength='4' placeholder='YYYY'>		
		</div>
		<button type='submit' id='verify-with-input'>Enter</button>
		<p>Or</p>
		<button type='submit' id='verify-with-fb'>Verify with Facebook</button>
	</form>
	
	<div class='footer-links'>
		<a href='/privacy-policy'>Privacy Policy</a>
		<a href='/terms-and-conditions'>Terms &amp; Conditions</a>
	</div>
</div>