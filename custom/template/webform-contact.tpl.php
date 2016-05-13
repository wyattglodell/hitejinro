<?=$webform_js?>
<div class="container">
	<div class="title-wrapper">
		<h1 class="page-title">Contact us</h1>
		<p class="copy">Questions? Comments? Concerns? Praise? Feel free to
	contact <?php echo ucfirst($site) ?> anytime and let us know what your thinking.</p>
	</div>

	<form method="post" action="<?=$url?>" id="webform-contact">
		<input type='hidden' name='action' value='submit-contact' />
		<ul class='form-ul'>
			<li id='webform-contact-name'>
				<!-- <label for='webform-name'><span class='req'>*</span>Name</label> -->
				<input id='webform-name' type='text' name='name' placeholder='Name' value='<?=$form['name']?>'/>
			</li>
			<li id='webform-contact-email'>
				<!-- <label for='webform-email'><span class='req'>*</span>Email</label> -->
				<input id='webform-email' type='text' name='email' placeholder='E-mail address' value='<?=$form['email']?>'/>
			</li>
			<li id='webform-contact-message'>
				<!-- <label for='message'><span class='req'>*</span>Message</label> -->
				<textarea name='message' rows='5' cols='30' placeholder='Message'><?=$form['message']?></textarea>
			</li>

			<li class='field-submit'>
				<input type='submit' name='submit' value='Submit' />
			</li>
		</ul>
	</form>
</div>