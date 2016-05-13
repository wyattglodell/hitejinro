<?php	
	if (user_access('Upload Files', 'File Manager')) {	
		$fm = new Filemanager;
	
		$uploaded = 0;
		
		if ($_POST['current_folder']) {
			$current_folder = $_POST['current_folder'];
			if (substr($current_folder, 0, strlen($conf->upload_dir)) == $conf->upload_dir) {
				$current_folder = substr($current_folder, strlen($conf->upload_dir));	
			}
			
			if (preg_match('~\.\./~', $current_folder)) {
				$func->log('Bad folder name uploaded ('.$current_folder.')', $_FILES, 'severe');
				die;	
			}
		}
		if ($_FILES['file']) {			
			$upload_dir = $func->build_path($conf->upload_file,$current_folder);

			if (!file_exists($upload_dir)) {
				$success = mkdir($upload_dir, 0777, true);
				if ($success) {
					chmod($upload_dir, 0777);
				}
			}

			if (is_writable($upload_dir)) {
				$thumbnails = $conf->thumbnails;
				
				if ($_SESSION['thumbnails']) {
					foreach ($_SESSION['thumbnails'] as $k=>$v)
					{
						$thumbnails[$k] = $v;	
					}
				}
				
				foreach ($_FILES['file']['size'] as $k=>$v)
				{					
					$file['size'] = $_FILES['file']['size'][$k];
					$file['name'] = $_FILES['file']['name'][$k];
					$file['type'] = $_FILES['file']['type'][$k];
					$file['tmp_name'] = $_FILES['file']['tmp_name'][$k];
					
					if ($file['size']) {
						if ($fm->valid_extension($file['name'])) {
							$uploaded = $renamed_file = false;
							$original_name = $file['name'];
							
							$file['name'] = $fm->clean_filename($file['name']);
							
							$file_name = $fm->get_unique_name($upload_dir, $file['name']);

							if ($file['name'] != $file_name || $original_name != $file['name']) {
								$renamed_file = true;	
							}
							
							if ($renamed_file && !$_POST['inline_upload']) {
								$renamed[] = 'File renamed from '.$original_name.' to '.$file_name;
							}
							
							$file['name'] = $file_name;
							$copy_file = true;
							
							$img = new Image($file);
							if ($img->is_img() && ($conf->thumbnails || $_SESSION['thumbnails'])) {
								$thumbnails = array_merge((array)$conf->thumbnails, (array)$_SESSION['thumbnails']);
								
								foreach ($thumbnails as $name=>$dim)
								{
									$data = explode('@', $dim);
									$d = explode('x', $data[0]);
									$width = (int) $d[0];
									$height = (int) $d[1];
									$aspect = $data[1];

									$img->resize($width,$height, $upload_dir.'/'.$conf->thumbnail_prefix.$name, $aspect);
								}
							}
															
							if (move_uploaded_file($file['tmp_name'], $upload_dir.'/'.$file['name'])) {
								chmod($upload_dir.'/'.$file['name'], 0777);
								$uploaded = true;
								$upload_counter++;
								
								$func->log('A file has been uploaded', $upload_dir.'/'.$file['name']);
								$uploaded_file_name[] = $func->build_path($current_folder, $file['name']);
							}
						} else {
							$invalid_ext[] = $file['name'];
						}
					}
				}
			} else {
				$msg = 'Permission error, could not write to upload folder.';
			}
		}
		
		if ($invalid_ext) {
			$msg = implode(', ', $invalid_ext).' is not an accepted file type';
		}
		
		if ($_POST['inline_upload']) {
			if ($uploaded_file_name) {
				$ufn = json_encode(array('status'=>true, 'files'=>$uploaded_file_name));	
			} else {
				$msg = 'No files uploaded';	
			}
		} else if (!$msg) {
			if (!$uploaded) {
				$msg = 'No files uploaded';
			} else { 
				if ($renamed) {
					$msg = implode(', ', $renamed); 
				} else {
					$msg = 'File'.($upload_counter > 1 ? 's' : '').' uploaded successfully'; 
				}
			}
		}
	} else {
		$uploaded = 0;
		$msg = 'You do not have permission to upload files';	
	}
	
	if ($_POST['inline_upload']) {
		echo $ufn;
	} else {
		echo "<script type='text/javascript'>parent.file_upload_status('".str_replace("'", "\'", $msg)."', $uploaded);</script>";
	}
	exit;
?>