<div style='padding: 20px; width: 300px;'>
	<form method='post' action='<?php echo $url ?>'>
		<input type='hidden' name='detail_log_search' value='1'>
		<table cellspacing="1" cellpadding="0" class="admin-search-tbl">
			<tbody>
				<tr>
					<td>
						<label>Start Date</label>
						<input type="text" name="start_date" placeholder="Start Date" class="text date datepicker" value='<?php echo date('m/d/Y', strtotime('-7 Day')) ?>'>
					</td>
					<td>
						<label>End Date</label>
						<input class="text date datepicker" type="text" name="end_date" placeholder="End Date" value='<?php echo date('m/d/Y') ?>'>
					</td>
				</tr>
				<tr>
					<td>
						<label>Detail</label>
						<input type='text' name='detail_search' class='text'>
					</td>
					<td>
								<button type='submit' class='submit btn'>Search</button>

					</td>
				</tr>
			</tbody>
		</table>
	</form>
</div>
<script type='text/javascript'>
	$('.datepicker').datepicker();
</script>	