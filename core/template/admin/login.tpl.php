<div style='width: 275px; margin: 50px auto; padding: 15px;' class='admin-bg'>
	<form method='post' action='<?php echo $login_destination?>'>
		<input type='hidden' name='action' value='login' />
		<table align='center' class='login-tbl'>
			<tr>
				<td style='color: black; font-size: 14px;'>Username &nbsp;</td>
				<td><input type='text' name='username' class='form-text' style='font-size: 14px;border:0; padding: 5px 10px;'/></td>
			</tr>
			<tr>
				<td style='color: black;font-size: 14px;'>Password &nbsp;</td>
				<td><input type='password' name='password' class='form-text' style='font-size: 14px;border:0; padding: 5px 10px;'/></td>
			</tr>
			<tr><td class='br'></td></tr>
			<tr>
				<td align='center' colspan='2'><input type='submit' name='submit' value='Login' class='btn' /></td>
			</tr>
		</table>
	</form>
</div>
