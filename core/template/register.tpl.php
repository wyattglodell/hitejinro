<form action="<?=$url?>" method="post" name="register" id='register-form'>
		<ul class='form-ul'>
			<li>
				<label>Username:</label>
				<input type='text' name='username' value='<?=$form['username']?>' />
			</li>
			<li>
				<label>Password:</label>
				<input type='password' name='password' value='<?=$form['password']?>' />
			</li>
			<li>
				<label>Confirm Password:</label>
				<input type='password' name='password_confirm' value='<?=$form['password_confirm']?>' />
			</li>
			<li>
				<label>Email Address:</label>
				<input type='text' name='email' value='<?=$form['email']?>' size='35'/>
			</li>	
		
			<li>
				<label>Name:</label>
				<input type='text' name='name' value='<?=$form['name']?>' />
			</li>
			<li>
				<label>Company:</label>
				<input type='text' name='company' value='<?=$form['company']?>' />
			</li>
			<li>
				<label>Title:</label>
				<input type='text' name='title' value='<?=$form['title']?>' />
			</li>
			<li>
				<label>Address 1:</label>
				<input type='text' name='address1' value='<?=$form['address1']?>' size='40'/>
			</li>
			<li>
				<label>Address 2:</label>
				<input type='text' name='address2' value='<?=$form['address2']?>' size='40'/>
			</li>
			<li>
				<label>City:</label>
				<input type='text' name='city' value='<?=$form['city']?>' size='10'/>
				State:
				<select name='state'>
					<option value=''> -- Please Select -- </option>
					<?php
						if ($states) {
							foreach ($states as $k=>$v)
							{
								$sel = $form['state'] == $k ? "selected='selected'" : '';

								echo "<option value='$k' $sel>$v</option>";
							}
						}
					?>
				</select>
				Zip:
				<input type='text' name='zip' value='<?=$form['zip']?>' size='10' />
			</li>
			<li>
				<label>Phone Number:</label>
				<input type='text' name='phone' size='40' value='<?=$form['phone']?>' />
			</li>
			<input type='hidden' name='action' value='register' />
			<li><label>&nbsp;</label><input type='submit' name='submit' value='Submit' /></li>
										
		</ul>
</form>