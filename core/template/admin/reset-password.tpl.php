<div style='width: 300px; margin: 50px auto; padding: 15px;' class='admin-bg'>
    <p>
        Enter a new password to reset your account password
    </p>

	<form method='post' action='<?php echo $login_destination?>'>
		<input type='hidden' name='action' value='reset' />
        <input type='hidden' name='form_id' value='<?php echo $form_id ?>'>
		<table align='center' class='login-tbl'>
			<tr>
				<td style='color: black;font-size: 14px;'>Password &nbsp;</td>
				<td><input type='password' name='password' class='text' placeholder='password' style='font-size: 14px;border:0; padding: 5px 10px;' /></td>
			</tr>
			<tr>
				<td style='color: black;font-size: 14px;'>Verify &nbsp;</td>
				<td><input type='password' name='verify_password' class='text' placeholder='verify password' style='font-size: 14px;border:0; padding: 5px 10px;' /></td>
			</tr>
			<tr><td class='br'></td></tr>
			<tr>
				<td align='center' colspan='2'>
                    <button type='submit' class='btn'>Submit</button>
                    <button type='button' class='btn' onclick='window.location="<?php echo $admin_url ?>"'>Cancel</button>
                </td>
			</tr>
		</table>
	</form>
</div>
