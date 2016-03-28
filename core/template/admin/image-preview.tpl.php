<script type='text/javascript'>
	$(function() {
		$('#cancel').click(function() {
			<?php if ($close_action == 'ajax') { ?>
				popup('<?=$previous_page?>');
			<?php } else { ?>
				$.fancybox.close();
			<?php } ?>
		});
		
	});
</script>
<p class='center'>
	<input type='button' value='Close' id='cancel' />
</p>
<div class='center image-preview-box'>
	<img src='<?=$preview_image?>' width='<?=$dim[0]?>' height='<?=$dim[1]?>' alt='Image does not exist' />
</div>

