<script type='text/javascript'>	
	var max_upload_size = '<?php echo $max_upload_size ?>';
	var max_upload_size_formatted = '<?php echo $max_upload_size_formatted ?>';
	var file_upload_url = '<?php echo $filemanager_upload_url ?>';
	var path = '<?php echo $file_path?>';
	var upload = '<?php echo $file_upload?>';


	$(function() {
		if (typeof CKEDITOR != 'undefined') {
			CKEDITOR.config.toolbar = 'Custom';
			CKEDITOR.config.allowedContent = true; 
			CKEDITOR.config.height = 350;
			CKEDITOR.config.filebrowserBrowseUrl = '<?php echo $filemanager_url ?>';
			CKEDITOR.config.filebrowserImageWindowWidth = '850';
		}	
	});
</script>

<div id='manager-form-box'>
	<?=$html?>
</div>