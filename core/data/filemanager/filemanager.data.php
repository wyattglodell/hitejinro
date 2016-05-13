<?php 
	if (user_access('Access File Manager', 'File Manager')) {
		if ($get->file) {
			$fm = new Filemanager;
			$fm->download($conf->root.$conf->path.$conf->public_dir.$get->file);
		}	
		
		define ('UPLOAD_ROOT', $conf->root);
		define ('UPLOAD_PATH', $conf->path.$conf->public_dir); 
		define ('UPLOAD_FOLDER', $conf->upload_dir);
		define ('FILE_ROOT', $conf->base_http); 
		define ('FILE_PATH', $conf->public); 
		define ('FILE_FOLDER', $conf->upload_dir);
		
		define ('UPLOAD_DIR', UPLOAD_ROOT.UPLOAD_PATH);
		define ('FILE_DIR', FILE_ROOT.FILE_PATH);
		
		define ('INDENT_SIZE', 20); # pixles 
		
		define ('NUM_UPLOAD_FILES', 20);
		
		define('THUMBNAIL_PREFIX', $conf->thumbnail_prefix);

		$tpl->assign('ckeditor_func_num', $get->CKEditorFuncNum);

		$tpl->assign('fm_src', $conf->filemanager_module);
		$tpl->assign('filemanager_upload_handler', $conf->filemanager_upload);
		$tpl->assign('filemanager_ajax', $conf->filemanager_ajax);
		
		$tpl->assign('base_path', $conf->public);
	
		$tpl->set_template('filemanager_header', 'filemanager/header.tpl.php');
	
		$setting->load_time = 'Off';
	
		if ($get->inline) {
			$tpl->set_template('base', 'filemanager/inline.tpl.php');	
		} else {
			$tpl->set_template('base', 'filemanager/browser.tpl.php');	
		}
	} else {
		$func->load_data('404');	
	}
?>