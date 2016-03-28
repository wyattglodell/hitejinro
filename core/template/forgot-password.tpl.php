<div id='login-box'>
	<div class='login-logo'>
        Enter the email address associated with the account.
    </div>

	<form method='post' action='<?=$url?>'>
		<input type='hidden' name='action' value='reset' />
        <div class='form-field'>
        	<input type='text' name='email' size='10' class='form-text' placeholder='email' />
        </div>
        <div class='submit'>
        	<button type='submit'>Submit</button>
        </div>
	</form>
    
    <div class='forgot'>
    	<a href='<?php echo $https ?>'>Return to Login</a>
    </div>
</div>