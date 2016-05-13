<form method='post' action='<?php echo $url ?>'>
	<input type='hidden' name='action' value='submit'>
<?php 
	foreach ($widget as $v)
	{
		echo "
		<div class='widget-content'>
			<table cellspacing='0' class='form-tbl'>
				<tr>
					<td class='tab-content-td'>
						$v
					</td>
				</tr>
			</table>
		</div>";	
	}
?>			
	<div>&nbsp;</div>		
    <div class='form-field submit'><label>&nbsp;</label><button type='submit' class='submit btn'>Submit</button> <button type='button' class='cancel btn' onclick="window.location = '<?php echo $https.'/'.$page.'/'.$subpage; ?>';">Cancel</button></div>	
</form>