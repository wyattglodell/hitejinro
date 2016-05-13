<?php
	class Image extends Base
	{
		function __construct($source)
		{
			parent::__construct();
			
			$this->source = $source;
			
			$this->ext = strtolower(end(explode('.', $this->source['name'])));
			$this->file_name = $this->source['name'];
			list ($this->width, $this->height) = getimagesize($this->source['tmp_name']);
			$this->quality = 85;
		}
		
		function is_img()
		{
			if (!in_array($this->ext, array ('gif','jpg','png','jpeg'))) {
				return false;
			}
			
			if (!$this->width && !$this->height) {
				return false;	
			}
			
			return true;
		}
		
		function set_quality($var)
		{
			$this->quality = $var;	
		}
		
		function is_atleast($width, $height)
		{
			return $width <= $this->width || $height <= $this->height;
		}
		
		function move($dest)
		{
			move_uploaded_file($this->source['tmp_name'], $dest);	
		}
		
		function check_aspect($width, $height)
		{
			if ($this->width && $this->height && $width && $height) {
				return ($this->width / $width == $this->height / $height);	
			} else {
				return false;	
			}
		}
		
		function is_square()
		{
			return $this->width == $this->height;
		}
		
		function resize($width, $height, $destination_folder, $aspect='aspect')
		{
			$w = $this->width;
			$h = $this->height;
			
			if ($aspect == 'crop') {
				if (($width && $width < $w) && ($height && $height < $h)) {
					$img_ratio = $w / $h;
					$target_ratio = $width / $height;
					
					if ($img_ratio >= $target_ratio) {
						$new_h = $height;
						$new_w = $w / ($h / $height);	
					} else {
						$new_h = $h / ($w / $width);
						$new_w = $width;
					}
					
					
					if ($new_w && $new_h) {						
						return $this->image_crop($this->source['tmp_name'], $destination_folder, $this->source['name'], $this->source['type'], $w, $h, $new_w, $new_h, $width, $height);
					} else {
						return false;	
					}
				}
			} else {
				if (($width && $width < $w) || ($height && $height < $h)) {
					if ($aspect == 'fixed') {
						$new_w = $width;
						$new_h = $height;
					} else if ($aspect == 'aspect') {
						$temp_w = $w;
						$temp_h = $h;
						
						do {
							if (($width && $width < $temp_w) && ($height && $height < $temp_h)) {
								
								if ($temp_w < $temp_h) {
									$ratio = $width / $temp_w;
								} else {
									$ratio = $height / $temp_h;
								}
								$continue = true;
							} else if ($width && $width < $temp_w) {
								$ratio = $width / $temp_w;
								$continue = true;
							} else if ($height && $height < $temp_h) {
								$ratio = $height / $temp_h;
								$continue = true;
							} else {
								$continue = false;	
							}
							
							if ($continue) {
								$temp_w = $new_w = $ratio * $temp_w;
								$temp_h = $new_h = $ratio * $temp_h;
							}
	
						} while ($continue);
					}			
					
					if ($new_w && $new_h) {						
						return $this->image_resize($this->source['tmp_name'], $destination_folder, $this->source['name'], $this->source['type'], $w, $h, $new_w, $new_h);
					} else {
						return false;	
					}
				}
			}
		}
			
		function image_resize($src, $dir, $name, $userfile_type, $old_width, $old_height, $new_width, $new_height) 
		{
			$part = explode('.', $name); # break apart image string to attach suffix to end
			$ext = array_pop($part); # pull out the extension
			$image = implode($part).'.'.$ext; # new image string
			
			$upload_dir = $dir;
	
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
				case 'image/jpeg' : imagejpeg($dst, $upload_dir.'/'.$image, $this->quality); break;
				case 'image/pjpeg' : imagejpeg($dst, $upload_dir.'/'.$image, $this->quality); break;
				case 'image/png' : imagepng($dst, $upload_dir.'/'.$image); break;
				case 'image/x-png' : imagepng($dst, $upload_dir.'/'.$image); break;
				default : return array(true, $userfile_type.' is not a supported image file type');
			}
			
			
			return $upload_dir.'/'.$image;
		}
		
		function image_crop($src, $dir, $name, $userfile_type, $old_width, $old_height, $new_width, $new_height, $thumb_width, $thumb_height) {
			
			$part = explode('.', $name); # break apart image string to attach suffix to end
			$ext = array_pop($part); # pull out the extension
			$image = implode($part).'.'.$ext; # new image string
			
			$upload_dir = $dir;
	
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
	}
?>