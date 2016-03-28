	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<script type='text/javascript' src='<?php echo $fm_src ?>/js/fm.js'></script>
	<script type='text/javascript'>
		var fm = new filemanager();

		fm.closed_folder_img = '<?php echo $fm_src ?>/img/folder.gif';
		fm.open_folder_img = '<?php echo $fm_src ?>/img/openfolder.gif';
		
		fm.folder_hover_classname = 'folder-hover';
		fm.filemanager_src = '<?php echo $fm_src ?>';
		fm.ajax_url = '<?php echo $filemanager_ajax ?>';
		fm.base_path = '<?php echo $base_path ?>';
		fm.current_url = '<?php echo $url ?>';
		
		fm.file_base_url = '<?php echo FILE_DIR?>';
		fm.file_folder = '<?php echo FILE_FOLDER?>';
		fm.current_folder = '<?php echo UPLOAD_FOLDER?>';
		
		fm.upload_folder = '<?php echo UPLOAD_FOLDER?>';
		
		fm.thumbnail_suffix = '<?php echo THUMBNAIL_SUFFIX?>';
		
		fm.CKEditorFuncNum = '<?php echo $ckeditor_func_num ?>';
		
		fm.preview_width = 100;
		fm.preview_height = 100;
	</script>
	<script type='text/javascript' src='<?php echo $fm_src ?>/js/tablesort.js'></script>
	<script type='text/javascript' src='<?php echo $fm_src ?>/js/functions.js'></script>
	<link rel='stylesheet' type='text/css' href='<?php echo $fm_src ?>/css/fm.css' />
	<title>File Manager</title>