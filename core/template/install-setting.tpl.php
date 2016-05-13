<div id='install'>
	<h1>Framework Installation - Settings</h1>

	<form method='post' action='<?php echo $url?>'>
		<input type='hidden' name='action' value='install-setting' />
		<ul class='form-ul'>
			<li><strong>Admin</strong></li>
			<li><label>Username</label><input type='text' name='admin_username' value='<?php echo $form['admin_username']?>' size='50' /></li>
			<li><label>Password</label><input type='text' name='admin_password' value='<?php echo $form['admin_password']?>'  size='50' /></li>
			<li><label>Contact Name</label><input type='text' name='contact_name' value='<?php echo $form['contact_name']?>'  size='50' /></li>
			<li><label>Contact Email</label><input type='text' name='contact_email' value='<?php echo $form['contact_email']?>'  size='50' /></li>
			<li><label>Reply Email</label><input type='text' name='email_from' value='<?php echo $form['email_from']?>'  size='50' /></li>
			<li><label>Site Title</label><input type='text' name='site_title' value='<?php echo $form['site_title']?>'  size='50' /></li>
			<li><label>Meta Tags</label><input type='text' name='meta_tag' value='<?php echo $form['meta_tag']?>'  size='50' /></li>
			<li><label>Meta Description</label><input type='text' name='meta_description' value='<?php echo $form['meta_description']?>'  size='50' /></li>
			
			<li><label>&nbsp;</label><input type='submit' value='Submit' /></li>
		</ul>
	</form>
</div>