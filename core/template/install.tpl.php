<div id='install'>
	<h1>Framework Installation</h1>

	<form method='post' action='<?php echo $url?>'>
		<input type='hidden' name='action' value='install' />
		<ul class='form-ul'>
			<li><strong>Database</strong></li>
			<li><label>Host</label><input type='text' size='40' name='database_host' value='<?php echo $form['database_host']?>' /></li>
			<li><label>Username</label><input type='text' size='40' name='database_user' value='<?php echo $form['database_user']?>' /></li>
			<li><label>Password</label><input type='text' size='40' name='database_pass' value='<?php echo $form['database_pass']?>' /></li>
			<li><label>Name</label><input type='text' size='40' name='database_name' value='<?php echo $form['database_name']?>' /></li>
			<li><label>Table Prefix</label><input type='text' size='40' name='table_prefix' value='<?php echo $form['table_prefix']?>' /></li>
			<li><label>Dev Email</label><input type='text' size='40' name='dev_email' value='<?php echo $form['dev_email']?>' /></li>
			<li><label>Master Username</label><input type='text' size='40' name='master_username' value='<?php echo $form['master_username']?>' /></li>
			<li><label>Master Password</label><input type='text' size='40' name='master_password' value='<?php echo $form['master_password']?>' /></li>
			
			<li><label>&nbsp;</label><input type='submit' value='Submit' /></li>
		</ul>
	</form>
</div>