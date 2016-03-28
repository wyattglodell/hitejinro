<script type='text/javascript'>
	$(function() {
		var height = 0;
		var width = 0;
		$('.list-tbl').each(function() {
			if ($(this).height() > height) {
				height = $(this).height();
				width = $(this).width();
			}
		});
		
		$('#list').height(height);
		//$('#list').width(width);
		
		$('#groups .group').click(function() {
			var id = $(this).html();
			
			if (!$('#list #list-'+id).hasClass('active')) {
				$('#groups .group').removeClass('active');
				$(this).addClass('active');
				$('#list .list-tbl.active').fadeOut(500).removeClass('active');
				$('#list #list-'+id).fadeIn(500).addClass('active');
			}
		});
	});
	
	function submit_all()
	{
		$('.list-tbl').each(function() {
			if (!$(this).hasClass('active')) {
				$(this).css({visibility:'hidden',display:'table'});
			}
		});
		return true;
	}
</script>

<div id='options-box'>
	<?php
        if ($can_add) {
            echo "<a id='add' href='$add_url' class='action'>+Add Setting</a>";
        }
    ?>
    
    <?php if ($can_clear_cache) { ?>
		<a href='<?php echo $url?>?flush_cache=1' class='action control confirm' confirm_text='empty the cache'><span class='icon icon-spinner10'></span> Empty Cache</a>
    <?php } ?>
</div>
<div class='clear'></div>

<div id='site-settings'>
<form method='post' action='<?=$url?>' onsubmit='return submit_all();'>
	<input type='hidden' name='action'  value='update_settings' />
	<div id='groups'>
		<?php
			$num = 0;
			$count = count($form) - 1;
			foreach ($form as $group=>$rows)
			{
				$active = !$num ? "active" : '';
				$first = $num == 0 ? 'first' : '';
				$last = $count == $num ? 'last' : '';
				
				echo "<div class='group $active $first $last'>$group</div>";
				$num++;
			}
		?>
	</div>
	<div id='list'>
	<?php
		$num = 0;
		foreach ($form as $group=>$rows)
		{
			$active = !$num ? "active" : '';
			
			echo "<table cellspacing='1' cellpadding='5' class='list-tbl $active' width='100%' id='list-$group'>";
			foreach ($rows as $id=>$row)
			{
				$bg = $bg ? '' : "class='odd'";
				echo "<tr $bg><th width='15%'>$row[label]</th><td width='30%'>$row[field]</td><td width='55%'>".nl2br($row['info'])."</td></tr>";
			}
			echo "</table>";
			$num++;
		}
	?>
	</div>
	
	<div class='clear'></div>
    <div class='br2'></div>
	<input type='submit' value='Update' class='submit btn'>
</form>
</div>
