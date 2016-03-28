<?=$tpl->admin_filter?>

<div class='clear'></div>


<?php
	if ($pagination) {
		echo "<div class='pagination'>";
		
		if ($pagination['prev_chapter']) {
			echo "<a class='chapter' href='$prev_chapter'>&laquo;</a>";
		}
		
		if ($pages) {
			foreach ($pages as $k=>$v)
			{
				$here = $current_page == $k ? 'here' : '';
				echo "<a class='$here' href='$v'>$k</a>";
			}
		}
		
		if ($pagination['next_chapter']) {
			echo "<a class='chapter' href='$next_chapter'>&raquo;</a>";
		}
		
		echo "</div>";
	}
?>

<div id='options-box'>
	<?php if ($allow_add) { ?>
		<a href='<?=$add_url?>' id='add' class='action'>+New <?=$keyword?></a>
	<?php } ?>
	
	<?php 
		if ($top_links) { 
			foreach ($top_links as $k=>$v)
			{
				echo " &nbsp; <a href='$v[url]' class='$v[class] action' confirm_text='$v[title]'><span class='icon $v[icon]'></span> $k</a>";
			}
		}
	?>
</div>

<div class='clear'></div>

<div id='description-box'>
	<?=$description_text?>
</div>

<table cellspacing='0' cellpadding='0' width='100%' class='default-tbl' id='default-tbl'>
	<tr>
		<td valign='top'>
			<table cellspacing='1' cellpadding='0' width='100%' class='sortable' id='tbl'>
				<thead>
					<tr>
						<?php
							if ($fields['header']) {
								foreach ($fields['header'] as $v)
								{
									$w = $v['width'] ? "width='$v[width]'" : '';
									echo "<th $w align='".$v['align']."'>$v[label]</th>";
								}
							}
						?>
						
						<?php if ($control_links) { 
						
						
								if ($control_links) {
									foreach ($control_links as $k=>$v)
									{
										echo "<th align='center' width='15'></th>";
									}
								}

						
						} ?>
					</tr>
				</thead>
				<tbody>
					<?php
						if ($fields['data']) {
							foreach ($fields['data'] as $id=>$row)
							{
								$bg = $bg ? '' : "odd";
								echo "<tr class='$bg rows'>";
								foreach ($row as $k=>$v)
								{
									$align = $fields['header'][$k]['align'];
									
									if ($row['link']) {
										echo "<td align='$align'>$row[link]</td>";
									} else {
										echo "<td align='$align'>$v</td>";
									}
								}
								if ($control_links) {
									foreach ($control_links as $k=>$v)
									{
										echo "<td align='center'>";
										echo "<a href='$v[url]$id' class='control $v[options]' confirm_text='$v[label]'><span class='icon $v[icon] control-link-icon'></span></a><br/>";
										echo "</td>";
									}
								}
								
								
								
								echo "</tr>";
							}
						
						} else {
							echo "<tr><td align='center' colspan='".(count($fields['header']) + 1)."'>No records found</td></tr>";
						}
					?>
				</tbody>
			</table>
		</td>
	</tr>
</table>

<?php
	if ($pagination) {
		echo "<div class='br2'></div><div class='pagination'>";
		
		if ($pagination['prev_chapter']) {
			echo "<a class='chapter' href='$prev_chapter'>&laquo;</a>";
		}
		
		if ($pages) {
			foreach ($pages as $k=>$v)
			{
				$here = $current_page == $k ? 'here' : '';
				echo "<a class='$here' href='$v'>$k</a>";
			}
		}
		
		if ($pagination['next_chapter']) {
			echo "<a class='chapter' href='$next_chapter'>&raquo;</a>";
		}
		
		echo "</div><div class='clear'></div><div class='br2'></div><div class='br2'></div>";
	}
?>