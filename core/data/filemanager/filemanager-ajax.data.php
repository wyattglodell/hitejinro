<?php
	if (user_access('Access File Manager', 'File Manager')) {
		define ('UPLOAD_ROOT', $conf->root); 
		define ('UPLOAD_PATH', $conf->public_dir); 
		define ('UPLOAD_FOLDER', $conf->upload_dir);
		define ('FILE_ROOT', $conf->base_http); 
		define ('FILE_PATH', $conf->public); 
		define ('FILE_FOLDER', $conf->upload_dir);
		
		define ('FM_SRC', $conf->filemanager_module);
		
		define ('UPLOAD_DIR', UPLOAD_ROOT.UPLOAD_PATH);
		define ('FILE_DIR', FILE_ROOT.FILE_PATH);
		
		define ('INDENT_SIZE', 20); # pixles 
		
		define ('NUM_UPLOAD_FILES', 20);
		
		define ('OPEN_FOLDER_IMG', 'img/openfolder.gif');
		define ('CLOSED_FOLDER_IMG', 'img/folder.gif');
		
		define('THUMBNAIL_PREFIX', $conf->thumbnail_prefix);
	
		$fm = new Filemanager;

		if ($get->fm_request == 'move_file') {				
			$files = explode('|', $_GET['file']);
			$dir = rtrim(UPLOAD_DIR, '/').'/'.ltrim($_GET['current_folder'], '/');
			$new_dir = rtrim(UPLOAD_DIR, '/').'/'.ltrim($_GET['destination_folder'], '/');
			
			if ($files) {
				foreach ($files as $v)
				{
					if (is_file($dir.'/'.$v) && $_GET['destination_folder']) {
						rename( $dir.'/'.$v, $new_dir.'/'.$v);
						
						if ($_SESSION['thumbnails']) {
							foreach ($_SESSION['thumbnails'] as $thumb_name=>$dim)
							{
								if (file_exists($dir.'/'.THUMBNAIL_PREFIX.$thumb_name.'/'.$v)) { 
									
									if (!file_exists($new_dir.'/'.THUMBNAIL_PREFIX.$thumb_name)) { 
										#echo 'created '.$new_dir.'/'.THUMBNAIL_PREFIX.$thumb_name.':';
										mkdir($new_dir.'/'.THUMBNAIL_PREFIX.$thumb_name);
										chmod($new_dir.'/'.THUMBNAIL_PREFIX.$thumb_name, 0777);
									}
									
									#echo $dir.'/'.THUMBNAIL_PREFIX.$thumb_name.'/'.$v.'--';
									
									rename($dir.'/'.THUMBNAIL_PREFIX.$thumb_name.'/'.$v, $new_dir.'/'.THUMBNAIL_PREFIX.$thumb_name.'/'.$v);
								}
							}
						}
						
						$moved++;
					}
				}	
			}
			
			if ($moved) {
				echo $moved. ' files moved successfully';
			} else {
				echo 'ERROR|Could not move files due to an internal error.';
			}
		} else if ($get->fm_request == 'folder_list') {		
			if (is_dir(UPLOAD_DIR.UPLOAD_FOLDER)) {
				$folders = $fm->dir_list(UPLOAD_DIR.UPLOAD_FOLDER);
				if ($folders) {
					$list[] = 'upload';
					$fm->dir_map($folders, '/upload', $list);
					echo implode('|', $list);
				}
			}
		} else if ($get->fm_request == 'update_folder_list' && $_GET['current_folder']) {		
			if (is_dir(UPLOAD_DIR.UPLOAD_FOLDER)) {
				$folders = $fm->dir_list(UPLOAD_DIR.UPLOAD_FOLDER);
	
				echo $fm->display_folder($folders, 0, UPLOAD_FOLDER, $_GET['current_folder']);
			} else {
				echo 'ERROR|Upload directory could not be found|Error retrieving folders';
			}
		} else if ($get->fm_request == 'update_file_list' && $_GET['current_folder']) {
			$dir = UPLOAD_DIR.$_GET['current_folder'];
			
			if (RETURN_FULL_URL) {
				$http = FILE_DIR.$_GET['current_folder'];
			} else {
				$http = FILE_PATH.$_GET['current_folder'];
			}
			
			if (is_dir($dir)) {
				$files = $fm->file_list($dir, $http);
	
				if ($files) {
					echo $fm->display_files($files);
				} else {
					echo 'ERROR|No Files Found|No Files Found';
				}
			} else {
				echo 'ERROR|Upload directory could not be found|Error retrieving folders';
			}		
		} else if ($get->fm_request == 'create_folder' && $_GET['current_folder'] && $_GET['new_folder']) {
			$dir = UPLOAD_DIR.$_GET['current_folder'];
	
			if (is_writable($dir)) {
				if (valid_foldername($_GET['new_folder'])) {
					if (is_dir($dir.'/'.$_GET['new_folder'])) {
						echo 'ERROR|This folder already exists, please choose another name';
					} else {
						mkdir($dir.'/'.$_GET['new_folder']);
						chmod($dir.'/'.$_GET['new_folder'], 0777);
						echo 'SUCCESS';
					}
				} else {
					echo 'ERROR|Only alphanumeric, dash, and underscore characters are allowed';
				}
			} else {
				echo 'ERROR|Folder could not be written to, please check the permissions';
			}
		
		} else if ($get->fm_request == 'delete_folder' && $_GET['current_folder']) {
			$dir = UPLOAD_DIR.$_GET['current_folder'];
	
			if (is_writable($dir) && is_dir($dir)) {
				if ($_GET['current_folder'] == UPLOAD_FOLDER) {
					echo 'ERROR|Cannot delete the root folder';
				} else {
					$fm->delete_dir($dir);
					echo 'SUCCESS';
				}
			} else {
				echo 'ERROR|Folder could not be written to, please check the permissions';
			}
		
		} else if ($get->fm_request == 'rename_folder' && $_GET['current_folder'] && $_GET['rename_folder']) {
			$dir = UPLOAD_DIR.$_GET['current_folder'];
			if (is_writable($dir) && is_dir($dir)) {
				if ($_GET['rename_folder'] == UPLOAD_FOLDER || '/'.$_GET['rename_folder'] == UPLOAD_FOLDER) {
					echo 'ERROR|Cannot rename the root folder';
				} else {
					if ($fm->valid_foldername($_GET['rename_folder'])) {
						$f = explode('/', $_GET['current_folder']);
						$f = array_splice($f, 0, -1);
						$fol = ltrim(implode('/', $f), '/');
						
						$new_dir = UPLOAD_DIR.'/'.$fol.'/'.ltrim($_GET['rename_folder'], '/');
	
						if (is_dir($new_dir)) {
							echo 'ERROR|A folder already exists with the name '.$_GET['rename_folder'];
						} else {
							rename($dir, $new_dir);
							echo 'SUCCESS';
						}
					} else {
						echo 'ERROR|Only alphanumeric, dash, and underscore characters are allowed';
					}
				}
			} else {
				echo 'ERROR|Folder could not be written to, please check the permissions';
			}
		} else if ($get->fm_request == 'delete_file' && $_GET['file'] && $_GET['current_folder']) {
			$dir = UPLOAD_DIR.$_GET['current_folder'];
			
			if (is_writable($dir) && is_dir($dir)) {
				$files = explode('|', $_GET['file']);
				
				foreach ($files as $v)
				{
					if (trim($v)) {					
						if ($_SESSION['thumbnails']) {
							foreach ($_SESSION['thumbnails'] as $thumb_name=>$dim)
							{
								if (file_exists($dir.'/'.THUMBNAIL_PREFIX.$thumb_name.'/'.$v)) { # remove thumbs
									unlink($dir.'/'.THUMBNAIL_PREFIX.$thumb_name.'/'.$v);
								}
							}
						}
					
						if (file_exists($dir.'/'.$v)) {
							unlink($dir.'/'.$v);
							$deleted++;
							$del = $v;
						}
					}	
				}
				
				if ($deleted == 1) {
					echo $del.' has been deleted successfully';
				} else {
					echo $deleted.' files deleted successfully';
				}
			} else {
				echo 'ERROR|Folder could not be written to, please check the permissions';
			}
		} else if ($get->fm_request == 'rename_file' && $_GET['old_name'] && $_GET['new_name'] && $_GET['current_folder']) {
			$dir = UPLOAD_DIR.$_GET['current_folder'];
			
			if (is_writable($dir) && is_dir($dir)) {
				if (file_exists($dir.'/'.$_GET['old_name'])) {
					if (!file_exists($dir.'/'.$_GET['new_name'])) {
						rename($dir.'/'.$_GET['old_name'], $dir.'/'.$_GET['new_name']);
						
						if ($_SESSION['thumbnails']) {
							foreach ($_SESSION['thumbnails'] as $thumb_name=>$dim)
							{
								if (file_exists($dir.'/'.THUMBNAIL_PREFIX.$thumb_name.'/'.$_GET['old_name'])) {
									rename($dir.'/'.THUMBNAIL_PREFIX.$thumb_name.'/'.$_GET['old_name'], $dir.'/'.THUMBNAIL_PREFIX.$thumb_name.'/'.$_GET['new_name']);
								}
							}
						}
	
						
						
						$old_thumb = $dir.'/'.$fm->get_thumb($_GET['old_name']);
						
						if (file_exists($old_thumb)) { # rename the thumb as well
							
							$new_thumb = $dir.'/'.$fm->get_thumb($_GET['new_name']);
							
							rename($old_thumb, $new_thumb);
						}
						
						echo $_GET['old_name'].' has been renamed '.$_GET['new_name'].' successfully';
					} else {
						echo 'ERROR|New file name already exists';
					}
				}
			} else {
				echo 'ERROR|Folder could not be written to, please check the permissions';
			}
		}
	} 
	exit;
?>