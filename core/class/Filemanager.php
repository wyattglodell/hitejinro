<?php
	class Filemanager extends Base
	{
		function valid_extension($file)
		{
			$ext = $this->get_extension($file);
			
			$allowed = array('gif', 'jpg', 'jpeg', 'png', 'tif', 'zip', 'sit', 'rar', 'csv', 'docx', 'gz', 'tar', 'htm', 'html', 'mov', 'mpg', 'avi', 'asf', 'mpeg', 'wmv', 'aif', 'eps','psd','aiff', 'wav', 'mp3', 'swf', 'ppt', 'rtf', 'doc', 'pdf', 'xls', 'txt', 'xml', 'xsl', 'dtd');	 
			
			if ($_POST['fm_ext'] == 'image') {
				$allowed = array ('jpg','jpeg','tiff','png','gif');	
			} else if ($_POST['fm_ext'] == 'document') {
				$allowed = array ('doc','txt','docx','xls','csv','rtf','ppt');	
			} 
			
			if ($ext) {
				return in_array(strtolower($ext), $allowed);
			} else {
				return false;
			}
		}	
		
		function get_name($file)
		{
			return substr($file, 0, strrpos($file, '.')); #end(explode('/', $file));	
		}

		function get_extension($file)
		{
			$f = explode('.', $file);
			return strtolower(end($f));
		}
		
		function get_unique_name($dir, $file)
		{
			if (file_exists($dir.'/'.$file)) {
				$file_exists_counter = 1;
				$file_name = $this->get_name($file);
				
				$ext = $this->get_extension($file);
				
				if ($ext) {
					$ext = '.'.$ext;	
				}
				
				do {
					$file = $file_name.'-'.($file_exists_counter++).$ext;
				} while (file_exists($dir.'/'.$file));
			}
			
			return $file;
		}
		
		function get_thumb($file)
		{
			$ext = $this->get_extension($file);
			$name = $this->get_name($file);
			return $name.THUMBNAIL_SUFFIX.'.'.$ext;
		}
		
		function js_escape($str)
		{
			return str_replace("'", "\'", $str);
		}
	
		function valid_foldername($fol)
		{
			return preg_match('~^[a-zA-Z0-9-_]+$~', $fol);
		}
		
		function clean_filename($str)
		{
			$str = preg_replace(array('~\s+~', '~-{2,}~'), array('-', '-'), $str);
			return preg_replace('~[^a-zA-Z0-9()\.\-_]~', '', $str);
		}
		
		function dir_list($dir)
		{
			$folders = array();
			$dh = opendir($dir);
			
			while (($file = readdir($dh)) !== false) {
				$fol[] = $file;
			}
			
			if ($fol)
				natcasesort($fol);
			
			foreach ($fol as $file)
			{
				if ($file != '..' && $file != '.') {
					if (is_dir($dir.'/'.$file)) {
						$folders[$file] = $this->dir_list($dir.'/'.$file);
					}
				}
			}
			
			return $folders;
		}
		
		function dir_map($dirs, $curr, &$list)
		{
			foreach ($dirs as $k=>$v)
			{
				if (strpos($k, THUMBNAIL_PREFIX) === false) {
					$list[] = ltrim($curr.'/'.$k, '/');
				
					if ($v && is_array($v)) {
						$this->dir_map($v, ltrim($curr.'/'.$k), $list);
					}
				}
			}
		}
		
		function file_list($dir, $http)
		{
			$f = array();
			$dh = opendir($dir);
			
			while (($file = readdir($dh)) !== false) {
				$fol[] = $file;
			}
			
			if ($fol)
				natcasesort($fol);
			
			foreach ($fol as $file)
			{
				if ($file != '..' && $file != '.') {
					if (is_file($dir.'/'.$file)) {
						#if (!preg_match('~.*?'.THUMBNAIL_SUFFIX.'\..*?~', $file)) { # hide the thumbnails
							if ($this->valid_extension($file)) {
								$data = array();
								$data['type'] = $this->get_file_type($file);
								$data['name'] = $file;
							
								if ($data['type'] == 'img') {
									list($w, $h) = getimagesize($dir.'/'.$file);
									$data['dimension'] = $w.'x'.$h;
									
									$thumb = $this->get_thumb($file);
									
									if (file_exists($dir.'/'.$thumb)) {
										$data['thumb'] = $http.'/'.$thumb;
									} else {
										$data['thumb'] = $http.'/'.$file;
									}
								} else {
									$data['dimension'] = '';
								}
								
								$data['size'] = $this->format_file_size(filesize($dir.'/'.$file));
								$data['path'] = $http.'/'.$file;
								$f[] = $data;
							}
						}
					#}
				}
			}
			
			return $f;
		}
		
		function format_file_size($byte)
		{
			if ($byte < 1000) return $byte.'B';
			
			if ($byte < 1000000) return floor($byte / 1000).'K';
			
			return floor($byte / 1000 / 1000). 'M';
		}
		
		function get_file_type($file)
		{
			switch(strtolower(end(explode('.',$file)))) { # insert the last element in array to find out what file type we're looking at
				case 'jpg' 			: 		$type = 'img'; break;
				case 'jpeg' 		: 		$type = 'img'; break;
				case 'gif' 			: 		$type = 'img'; break;
				case 'png' 			: 		$type = 'img'; break;
				case 'bmp'			:		$type = 'img'; break;
				case 'pdf' 			: 		$type = 'pdf'; break;
				case 'csv' 			: 		$type = 'excel'; break;
				case 'xl' 			: 		$type = 'excel'; break;
				case 'xls' 			: 		$type = 'excel'; break;
				case 'doc' 			: 		$type = 'doc'; break;
				case 'docx' 		: 		$type = 'doc'; break;
				case 'mov' 			: 		$type = 'video'; break;
				case 'avi' 			: 		$type = 'video'; break;
				case 'mpeg' 		: 		$type = 'video'; break;
				case 'wmv' 			: 		$type = 'video'; break;
				case 'txt' 			: 		$type = 'text'; break;
				case 'html' 		: 		$type = 'html'; break;
				case 'php' 			: 		$type = 'html'; break;
				case 'js' 			: 		$type = 'html'; break;
				case 'htm' 			: 		$type = 'html'; break;
				case 'html' 		: 		$type = 'html'; break;
				case 'css' 			: 		$type = 'html'; break;
				case '.htaccess'	: 		$type = 'html'; break;
				case 'zip' 			: 		$type = 'zip'; break;
				case 'gzip' 		: 		$type = 'zip'; break;
				case 'tar' 			: 		$type = 'zip'; break;
				case 'gz' 			: 		$type = 'zip'; break;
				case 'exe' 			: 		$type = 'exe'; break;
				case 'ppt'			: 		$type = 'ppt'; break;
				case 'psd'			:		$type = 'psd'; break;
				case 'sql'			:		$type =	'text'; break;
				default 			: 		$type = 'unknown';
			}
			
			return $type;
		}
		
		function display_folder($folders, $indent, $parent, $current_folder)
		{
			if ($parent == UPLOAD_FOLDER) {
				$open = UPLOAD_FOLDER == $current_folder ? 'open' : '';
				$r = "<div class='folder-item' id='folder-$parent'><img src='".FM_SRC."/img/{$open}folder.gif' /> <span class='folder-item-name'>".ltrim($parent, '/')."</span></div>";
			}
			
			$indent += 1;
			foreach ($folders as $k=>$v)
			{
				$open = $current_folder == $parent.'/'.$k ? 'open' : '';
				
				$thumbnail_dir = str_replace(THUMBNAIL_PREFIX, '', $k);
				
				if (strpos($k, THUMBNAIL_PREFIX) === false) { #$k == $thumbnail_dir || ($k != $thumbnail_dir && !$_SESSION['fm_thumbnails'][$thumbnail_dir])) {
					$r .= "<div class='folder-item' id='folder-$parent/$k' style='padding-left: ".($indent*INDENT_SIZE)."px'><img src='".FM_SRC."/img/{$open}folder.gif' class='folder-img'/> <span class='folder-item-name'>$k</span></div>";
				}
				
				if ($v && ($k == $thumbnail_dir || !$_SESSION['fm_thumbnails'][$thumbnail_dir])) {
					$r .= $this->display_folder($v, $indent, $parent.'/'.$k, $current_folder);
				}
			}
			
			return $r;
		}
		 
		function display_files(& $files)
		{
			if ($files) {
				$num = 0;
				
				$r = "<table cellspacing='1' cellpadding='0' width='100%' id='file-list-tbl' class='sortable'>";
				$r .= "<tr><th width='30' class='unsortable' id='check-all-th'><input type='checkbox' id='check-all-checkbox' /></th><th class='sortable'>Name</th><th width='30'  class='unsortable'>Type</th><th width='80'  class='unsortable'>Size</th></tr>";
				foreach ($files as $v)
				{
					
					$bg = $bg ? '' : "class='odd'";
					
					$type = $v['type'] == 'img' ? "onclick='show_preview($num)'" : "onclick='download_file($num)'";
					
					$r .= "
							<tr $bg>
								<td align='center'><input type='checkbox' id='file-$num-checkbox'/></td>
								<td><span id='file-$num-name' class='checkbox'>$v[name]</span> <span class='full-file-name' id='file-$num-path'>$v[path]</span><span class='full-file-name' id='file-$num-thumb'>$v[thumb]</span></td>
								<td align='center'><img src='".FM_SRC."/img/ext_$v[type].gif' alt='$v[dimension]' title='$v[dimension]' $type class='file-type-img'/></td>
								<td class='pad-right'><span id='file-$num-size'>$v[size]</span></td>
							</tr>";
					++$num;
				}
				
				$r .= "</table>";
				return $r;
			} else {
				return 'ERROR|No files found';
			}
		}
		
		function image_crop($src, $dir, $name, $thumbnail_name, $userfile_type, $old_width, $old_height, $new_width, $new_height, $thumb_width, $thumb_height) {
			
			$part = explode('.', $name); # break apart image string to attach suffix to end
			$ext = array_pop($part); # pull out the extension
			$image = implode($part).'.'.$ext; # new image string
			
			if ($thumbnail_name) {
				$upload_dir = $dir.THUMBNAIL_PREFIX.$thumbnail_name;
			} else {
				$upload_dir = $dir;
			}
	
			if (!is_dir($upload_dir)) {
				mkdir($upload_dir);
				chmod($upload_dir, 0777);
			} 
	
			$dst = imagecreatetruecolor($thumb_width, $thumb_height);
			
			//resize image
			switch ($userfile_type) {
				case 'image/gif' : $im = imagecreatefromgif($src); break;
				case 'image/jpeg' : $im = imagecreatefromjpeg($src); break;
				case 'image/jpg' : $im = imagecreatefromjpeg($src); break;
				case 'image/pjpeg' : $im = imagecreatefromjpeg($src); break;
				case 'image/png' : $im = imagecreatefrompng($src); imagealphablending($dst, false); imagesavealpha($dst, true); break;
				case 'image/x-png' : $im = imagecreatefrompng($src); imagealphablending($dst, false); imagesavealpha($dst, true); break; 
				default : return array(true, $userfile_type.' is not a supported image file type');
			}
			
			imagecopyresampled($dst, $im, (0 - ($new_width - $thumb_width) / 2), (0 - ($new_height - $thumb_height) / 2),0,0, $new_width, $new_height, $old_width, $old_height);
		
			switch ($userfile_type) {
				case 'image/gif' : imagegif($dst, $upload_dir.'/'.$image); break;
				case 'image/jpeg' : imagejpeg($dst, $upload_dir.'/'.$image, 85); break;
				case 'image/pjpeg' : imagejpeg($dst, $upload_dir.'/'.$image, 85); break;
				case 'image/png' : imagepng($dst, $upload_dir.'/'.$image); break;
				case 'image/x-png' : imagepng($dst, $upload_dir.'/'.$image); break;
				default : return array(true, $userfile_type.' is not a supported image file type');
			}
			
			chmod($upload_dir.'/'.$image, 0777);
			
			return array(false, '');
		}
		
		function image_resize($src, $dir, $name, $thumbnail_name, $userfile_type, $old_width, $old_height, $new_width, $new_height) {
			
			$part = explode('.', $name); # break apart image string to attach suffix to end
			$ext = array_pop($part); # pull out the extension
			$image = implode($part).'.'.$ext; # new image string
			
			if ($thumbnail_name) {
				$upload_dir = $dir.THUMBNAIL_PREFIX.$thumbnail_name;
			} else {
				$upload_dir = $dir;
			}
	
			if (!is_dir($upload_dir)) {
				mkdir($upload_dir);
				chmod($upload_dir, 0777);
			} 
	
			$dst = imagecreatetruecolor($new_width, $new_height);
			
			//resize image
			switch ($userfile_type) {
				case 'image/gif' : $im = imagecreatefromgif($src); break;
				case 'image/jpeg' : $im = imagecreatefromjpeg($src); break;
				case 'image/jpg' : $im = imagecreatefromjpeg($src); break;
				case 'image/pjpeg' : $im = imagecreatefromjpeg($src); break;
				case 'image/png' : $im = imagecreatefrompng($src); imagealphablending($dst, false); imagesavealpha($dst, true); break;
				case 'image/x-png' : $im = imagecreatefrompng($src); imagealphablending($dst, false); imagesavealpha($dst, true); break; 
				default : return array(true, $userfile_type.' is not a supported image file type');
			}
			
			imagecopyresampled($dst, $im, 0,0,0,0, $new_width, $new_height, $old_width, $old_height);
		
			switch ($userfile_type) {
				case 'image/gif' : imagegif($dst, $upload_dir.'/'.$image); break;
				case 'image/jpeg' : imagejpeg($dst, $upload_dir.'/'.$image, 85); break;
				case 'image/pjpeg' : imagejpeg($dst, $upload_dir.'/'.$image, 85); break;
				case 'image/png' : imagepng($dst, $upload_dir.'/'.$image); break;
				case 'image/x-png' : imagepng($dst, $upload_dir.'/'.$image); break;
				default : return array(true, $userfile_type.' is not a supported image file type');
			}
			
			chmod($upload_dir.'/'.$image, 0777);
			
			return array(false, '');
		}
		
		function delete_dir($dir) {
			if (!file_exists($dir)) return true;
			if (!is_dir($dir) || is_link($dir)) return unlink($dir);
			foreach (scandir($dir) as $item) {
				if ($item == '.' || $item == '..') continue;
				if (!$this->delete_dir($dir . "/" . $item)) {
					chmod($dir . "/" . $item, 0777);
					if (!$this->delete_dir($dir . "/" . $item)) return false;
				};
			}
			return rmdir($dir);
		}
		
		function download($filename)
		{
			if (preg_match('~\.\./~', $filename)) {
				die('Invalid file requested for download');
			}
			
			$file_extension = strtolower(substr(strrchr($filename,"."),1));
			
			if($filename == "") {
			  echo "<html><title>Error</title><body>ERROR: download file NOT SPECIFIED. </body></html>";
			  exit;
			}
			
			switch( $file_extension )
			{
			  case "pdf": $ctype="application/pdf"; break;
			  case "exe": $ctype="application/octet-stream"; break;
			  case "zip": $ctype="application/zip"; break;
			  case "doc": $ctype="application/msword"; break;
			  case "csv": $ctype="application/vnd.ms-excel"; break;
			  case "xls": $ctype="application/vnd.ms-excel"; break;
			  case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
			  case "gif": $ctype="image/gif"; break;
			  case "png": $ctype="image/png"; break;
			  case "jpeg":
			  case "jpg": $ctype="image/jpg"; break;
			  default: $ctype="application/force-download";
			}
			
			header("Pragma: public"); // required
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false); // required for certain browsers 
			header("Content-Type: $ctype");
			// change, added quotes to allow spaces in filenames, by Rajkumar Singh
			header("Content-Disposition: attachment; filename=\"".basename($filename)."\";" );
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize($filename));
			readfile("$filename");
			exit;
			
		}
	}
?>