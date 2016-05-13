<div id='password-reset'>
	<p>
        Enter a new password to reset your account password
    </p>

	<form method='post' action='<?=$url?>'>
		<input type='hidden' name='action' value='reset' />
        <input type='hidden' name='form_id' value='<?php echo $form_id ?>'>
        <div class='form-field'>
        	<input type='password' name='password' size='10' class='text' placeholder='password' />
        </div>
        <div class='form-field'>
        	<input type='password' name='verify_password' size='10' class='text' placeholder='verify password' />
        </div>
        <div class='form-field'>
        	<button type='submit' class='btn'>Submit</button>
        </div>
	</form>
</div>