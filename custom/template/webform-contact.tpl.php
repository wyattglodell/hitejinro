<?=$webform_js?>

<form method="post" action="<?=$url?>" id="webform-contact">
	<input type='hidden' name='action' value='submit-contact' />
	<ul class='form-ul'>
		<li id='webform-contact-name'>
			<label for='webform-name'><span class='req'>*</span>Name</label>
			<input id='webform-name' type='text' name='name' value='<?=$form['name']?>'/>
		</li>
		<li id='webform-contact-email'>
			<label for='webform-email'><span class='req'>*</span>Email</label>
			<input id='webform-email' type='text' name='email' value='<?=$form['email']?>'/>
		</li>
		<li id='webform-contact-message'>
			<label for='message'><span class='req'>*</span>Message</label>
			<textarea name='message' rows='5' cols='30'><?=$form['message']?></textarea>
		</li>

		<li class='field-submit'>
			<label>&nbsp;</label>
			<input type='submit' name='submit' value='Submit' />
			<input type='reset' value='Clear' />
			<span class='req'>*</span>Required Fields
		</li>
	</ul>
</form>