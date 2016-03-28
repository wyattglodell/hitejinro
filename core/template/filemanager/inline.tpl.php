<!doctype html>
<html>
<head>
	<?php echo $tpl->filemanager_header ?>
</head>

<body>
	<iframe id='upload_frame' name='upload_frame' src=''></iframe>
		 
	<form method='post' action='<?php echo $filemanager_upload ?>' target='upload_frame' enctype="multipart/form-data">
		<input type='file' name='file[]' multiple='multiple' value='' style='margin-top:8px;'/> <input type='submit' value='Upload' style='border: 0; color: #1f9cfd; background: none; cursor: pointer; '/>
		<input type='hidden' name='inline_upload' value='1' />
        <input type='hidden' name='fm_ext' value='<?php echo $fm_ext?>'>
		<input type='hidden' name='current_folder' value='<?=UPLOAD_FOLDER?>/imagecache_auto_upload' />
	</form>
	
	<input type='hidden' id='uploaded_file' />
	<script type='text/javascript'>
		fm.return_id = <?php echo intval($fm_id)?>;
	</script>	
</body>
</html>