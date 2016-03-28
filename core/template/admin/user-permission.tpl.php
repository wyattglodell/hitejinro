<?php if ($roles) { ?>
<form method='post' action='<?=$form_url?>'>
	<table cellspacing='0' cellpadding='0' class='tbl user-permission-tbl'>
		<?php
			foreach ($permissions as $group=>$rows)
			{
				echo "<tr>";		
				echo "<th align='left' width='70%'>$group</th>";
				foreach ($roles as $role)
				{
					if (!$count++) {
						echo "<th class='center'>$role[name]</th>";
					} else {
						echo "<th class='center'></th>";
					}
				}
				echo "<th></th>";
				echo "</tr>";
				
				foreach ($rows as $pid=>$action)
				{
					if ($action != 'Super Administrator' || user_level(0)) {
						$odd = $odd ? '' : 'odd';
						
						echo "<tr class='$odd rows'>";
						echo "<td class='pad-left'>$action</td>";
						
						foreach ($roles as $role)
						{
							$checked = $role_permission[$role['role_id']][$pid] ? "checked='checked'" : '';
							echo "<td align='center'><input $checked type='checkbox' name='perm[".$group."][".$role['role_id']."][$pid]' value='1'></td>";
						}
						
						if (user_level(0)) {
							echo "<td align='center'><a title='delete this item' class='control confirm ajax type-delete' href='$http/$page/$subpage?rid=$rid&amp;delete=$pid'><span class='icon icon-remove3'></span></a></td>";
						}
						
						echo "</tr>";
					}
				}
			}
		?>
	</table>
	<div class='br2'></div>
	<input type='hidden' name='action' value='permissions'>
	<button type='submit' class='btn'>Save</button>
</form>
<?php } else { ?>
	<p>Invalid Role ID requested.</p>


<?php } ?>