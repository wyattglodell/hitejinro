<style>
	table.addresses { font-family: 'Lato', arial, san-serif; }
	table.tbl tr td a { display: block;  font-size: 14px; text-decoration: none; }
	table.tbl tr td a.delete { float: right; visibility: hidden; }
	table.tbl tr td a strong { font-weight: 700; }
	
	table.tbl tr td:hover { background: #ffedbe; }
		table.tbl tr td:hover a.delete	{ visibility: visible;}
	
	.add-link a { margin-right: 15px; }
</style>

<p class='add-link '>
	<a href='<?php echo $https.'/'.$page.'/'.$subpage.'/edit/' ?>' class='action'>+ Add New Address</a>
	<a href='<?php echo $https.'/'.$page.'/'.$subpage.'/import/' ?>' class='popup action'><span class='icon icon-upload3'></span> Import CSV</a>
</p>
<?php
	if ($states) {
		echo "<p><form method='get' action='$url'><select name='state'><option value=''>Select State</option>";
			
		foreach ($states as $k=>$v)
		{
			$sel = $state_selected == $k ? "selected='selected'" : '';
			echo "<option value='$k' $sel>".$state_list[$k]." ($v)</option>";
		}
		echo "</select> <button type='submit' class='btn'>Select</button></form></p>";
	}
?>

<table class='tbl addresses' cellspacing='1' cellpadding='0' width='100%'>
    <?php
        if ($addresses) {
			
            foreach ($addresses as $k=>$v)
            {
				if ($k % 6 == 0) {
					echo "<tr>";	
				}
				
				$add2 = $v['address2'] ? '<br>'.$v['address2'] : '';
				
                echo "
				<td>
					<a href='$https/$page/$subpage/delete/$v[location_id]' class='delete'><span class='icon icon-remove2'></span></a>
					
					<a href='$https/$page/$subpage/edit/$v[location_id]' class=''><strong>$v[store_name]</strong><br>$v[address]$add2<br>$v[city], $v[state], $v[zip]<br>$v[country]</a>
				</td>";
								
				if ($k % 6 == 5) {
					echo "</tr>";	
				}
            }
			
			if ($k % 6 != 0) echo '</tr>';
        }
    ?>
</table>