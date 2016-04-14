<?=$webform_js?>

<form method="post" action="<?=$url?>" id="webform-mailinglist">
	<input type='hidden' name='action' value='submit-mailinglist' />
	<ul class='form-ul'>
		<li id='webform-mailinglist-email'>
			<label for='webform-email'><span class='req'>*</span>Email</label>
			<input id='webform-email' type='text' name='email' value='<?=$form['email']?>'/>
		</li>
		<li class='field-submit'>
			<label>&nbsp;</label>
			<input type='submit' name='submit' value='Submit' />
			<input type='reset' value='Clear' />
			<span class='req'>*</span>Required Fields
		</li>
	</ul>
</form>