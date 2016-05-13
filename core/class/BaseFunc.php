<?php
	class BaseFunc extends Base
	{
		function log($msg, $info='', $options = '')
		{			
			if ($info) {
				$data = addslashes(gzcompress(serialize($info)));
			}
			
			$opts = array();
			
			if ($options) {
				foreach (explode('|', $options) as $opt)
				{
					$opts[$opt] = true;	
				}
			}
			
			$url = str_replace($this->conf->base_http, '', ($opts['url'] ? $this->conf->current_url : $this->conf->current_uri));
			
			if (!$url) {
				$url = '/';	
			}
			
			if (in_array($opts['severe'], array('severe', 'warning', 'notice'))) {
				$severity = $opts['severe'];
			}
				
			$this->sql->query("
				INSERT INTO ".$this->conf->LOG."
					(message, uri, username, ip, time, data, severity) 
				VALUES 
					('".$this->sql->sanitize($msg)."', '".$this->sql->sanitize($url)."', '".User::get_username()."', '{$_SERVER['REMOTE_ADDR']}', NOW(),'$data', '$severity')");
			
			if ($opts['severe'] || $opts['warning']) {
				$this->email(($this->conf->dev_email ? $this->conf->dev_email : $this->setting->contact_email), 'Severe warning from '.$this->setting->site_name, "Message: ".$msg."\n\nReferrer:".$_SERVER['HTTP_REFERER']."\n\nURI: ".$url."\n\nUsername: ".User::get_username()."\n\nIP: ".$_SERVER['REMOTE_ADDR']."\n\nData:\n".print_r($info, true));
				if ($opts['severe']) {
					die ('A fatal error has occured. Please contact us for help.');
				}
			}
		}
		
		function error404($type)
		{
			$tpl = Registry::get('tpl');
			
			if ($type) { # add different types..
				
				
			} else {
				header('HTTP/1.1 404 Not Found');
				$tpl->set_template('content', 'page-not-found.tpl.php');
			}
		}
		
		function user_access($action, $group = '')
		{
			if (!user_access($action, $group)) {
				$this->redirect($this->conf->http, 'You do not have permissions to access this page');
			}
		}
						
		function get_img($img, $type)
		{
			$path = dirname($img);
			$image = basename($img);
			
			if ($type) {
				$path = $this->build_path($path, $this->conf->thumbnail_prefix.$type);
			}
			
			if (file_exists($this->build_path($this->conf->root, $path, $image))) {
				return $this->build_path($this->conf->path, $path, $image);
			} else {
				return $img;
			}
		}
		
		function build_path()
		{
			$paths = func_get_args();
			
			foreach ($paths 	as $k=>$path)
			{
				$path = trim($path, '/');
				
				if ($k == 0 && substr($path, 0, 4) == 'http') {
					$ret = $path;
				} else if ($path) {
					$ret .= '/'.$path;
				}
			}
			
			return $ret;
		}
		
		function add_edit_link(& $content, $manager, $id)
		{
			if (user_access('Access Page', 'Administrator') && $manager && $id) {
				return "<div class='admin-editable'><a href='".$this->href($this->conf->admin.'/'.$manager, "action=edit&id=$id&destination=".base64_encode($this->href($this->conf->current_url)))."'>[edit this content]</a></div>".$content;
			} else {
				return $content;	
			}
		}
		
		function set_meta(& $row)
		{
			$row['title'] = $row['title'] ? $row['title'] : $row['name'];
			$row['keyword'] = $row['keyword'] ? $row['keyword'] : $this->setting->meta_tag;
			$row['description'] = $row['keyword'] ? $row['description'] : $this->setting->meta_description;
			
			$tpl = Registry::get('tpl');
			
			if ($row['title']) {
				$tpl->assign('metatitle', $row['title'].' | '.$this->setting->site_name, true);
				$tpl->assign('page_title', $row['title'], true);
			} else {
				$tpl->assign('metatitle', $this->setting->site_name, true);
			}
			
			if ($row['keyword']) {
				$tpl->assign('metakeyword', $row['keyword'], true);
			}
			
			if ($row['description']) {
				$tpl->assign('metadescription', $row['description'], true);
			}
		}
		
		function get_content($alias, $set_meta = true, $edit_link = true)
		{
			if (substr($alias, 0, 3) == 'id/') {
				$where = "page_id = ".intval(end(explode('/', $alias)));	
			} else {
				$where = "alias = '".$this->sql->sanitize($alias)."'";	
			}
			
			$this->sql->query("SELECT * FROM {$this->conf->PAGE} WHERE $where AND status = 1");
			$row = $this->sql->fetch();
			
			if ($row['is_content'] && $edit_link) {
				$row['content'] = $this->add_edit_link($row['content'], 'pages', $row['page_id']);
			}
			
			if ($set_meta) {
				$this->set_meta($row);
			}
			
			return $row;
		}
		
		function clean_url($str, $token='-')
		{
			return strtolower(trim(preg_replace(array('~[\s_]+~', '~[^\w\d-]~', '~-{2,}~'), array($token, '', $token), $str), $token));
		}
		
		function alias($str, $tbl, $col, $ignore_self = '')
		{
			$num = 0;
			$pk_val = (int) $pk_val;

			if ($ignore_self) $ignore_self = 'AND '.$ignore_self;
			
			do {
				$title = $this->clean_url($str).$append;
				
				$this->sql->query("SELECT COUNT(*) FROM $tbl WHERE $col = '$title' $ignore_self");
				$cont = $this->sql->result(0);
				
				if ($cont) {
					$append = '-'.(++$num);
				}
			} while ($cont);
			
			return $title;
		}
		
		function format_url($url,$prefix='')
		{
			if (!$url) return '';
			
			if (preg_match('~^http(s)?://~', $url)) {
				return $url;
			} else {				
				if (preg_match('~^([a-zA-Z0-9-]+\.)+([a-zA-Z]+)~', $url)) {
					return 'http://'.$url;
				} else {
					$prefix = $prefix ? $prefix : $this->conf->http;
					return $prefix.'/'.ltrim($url,'/');
				}				
			}
		}
		
		function get_img_dim($old_width, $old_height, $new_width, $new_height)
		{
			$dim = array();
			if ($old_width > $old_height) { # image width is bigger, work off this
				$ratio = $new_width / $old_width; # proportion of the two.
				$dim['width'] = round($old_width * $ratio);
				$dim['height'] = round($old_height * $ratio);
			} else { # image height is bigger
				$ratio = $new_height / $old_height; # proportion of the two.
				$dim['width'] = round($old_width * $ratio);
				$dim['height'] = round($old_height * $ratio);
			}
			return $dim;
		}
				
		function email($to, $subject, $message, $header = '', $html = false)
		{
			if (!$header) $header = "FROM: {$this->setting->contact_name} <{$this->setting->from_email}>";
			
			if ($html) {
			    $header = "FROM: {$this->setting->contact_name} <{$this->setting->from_email}> \r\n";
				$header .= "MIME-Version: 1.0\r\n";
				$header .= "Content-type: text/html; charset=iso-8859-1\r\n";
			}
			return mail($to, $subject, $message, $header);
		}
		
		function validate($subject, $type)
		{
			switch ($type)
			{
				case 'email' :
					return filter_var($subject, FILTER_VALIDATE_EMAIL); 
					break;
				
				case 'phone' : # format 661-123-4567 x1234
					return preg_match('~^[0-9]{3}-[0-9]{3}-[0-9]{4}(\sx[0-9]+)?$~', $subject); 
					break;
			
			}
		}
		
		function random($length=9)
		{
			$valid[] = '48-57';
			$valid[] = '65-90';
			$valid[] = '97-122';
			
			for ($i=0; $i<=$length; $i++)
			{
				$num = rand(0,2);
				$rng = explode('-', $valid[$num]);
				
				$val .= chr(rand($rng[0],$rng[1]));
			}
			return $val;
		}
		
		function date_to_normal($d)
		{
			if (strpos($d, '-') !== false) {
				$date = explode('-', $d);
				if (checkdate($date[1],$date[2],$date[0])) { # yyyy-mm-dd
					return $date[1].'/'.$date[2].'/'.$date[0];
				} else if (checkdate($date[0],$date[1],$date[2])) { #mm-dd-yyyy
					return $date[0].'/'.$date[1].'/'.$date[2];
				}   
			} 
			
			return $d;
		}
		
		function date_to_sql($d, $validate = false)
		{
			if (strpos($d, '/') !== false) {
				$date = explode('/', $d);
				if (checkdate($date[0],$date[1],$date[2])) { # mm/dd/yyyy
					return $date[2].'-'.$date[0].'-'.$date[1];
				}  else if ($validate) {
					return false;	
				}
			} else if (strpos($d, '-') !== false) {
				$date = explode('-', $d);
				
				if (checkdate($date[0],$date[1],$date[2])) { #mm-dd-yyyy
					return $date[2].'-'.$date[0].'-'.$date[1];
				}  else if ($validate) {
					return false;	
				}
			} 
			
			return $d;
		}
		
		function load_data($_name, $_return = false)
		{
			$_name = $_name.'.data.php';
			
			if (file_exists($this->conf->custom.'/data/'.$_name)) {
				$_file = $this->conf->custom.'/data/'.$_name;	
			} else if (file_exists($this->conf->core.'/data/'.$_name)) {
				$_file = $this->conf->core.'/data/'.$_name;
			}

			if ($_file && !$_return) {
				foreach (Registry::retrieve_all() as $k=>$v)
				{
					$$k = $v;	
				}
					
				require ($_file);
				
				return true;
			} else if ($_file && $_return) {
				return $_file;
			} else {
				return false;
			}
		}
		
		
		function clean_name($str)
		{
			return ucwords(str_replace(array('_','-'), ' ', $str));
		}
				
		function time($value, $strip_meridiem = false) # accepts 06:00 AM || 0600
		{
			if (strpos($value, 'PM') || strpos($value, 'AM')) {	
				$temp = explode(' ', $value);
			
				$time  = preg_replace('~[^\d]~', '', $temp[0]);
				$time .= strlen($time) <= 2 ? '00' : '';	
				
				if ($temp[1] == 'PM' && $time < 1200) {
					$time += 1200;
				} else if ($temp[1] == 'AM' && $time >= 1200) {
					$time -= 1200;
				}
			} else {
				if ($value >= 1200) { 
					$value -= $value >= 1300 ? 1200 : 0;
					$meridiem = 'PM';
				} else {
					$value += $value < 100 ? 1200 : 0;
					$meridiem = 'AM';
				}
				
				$time = str_pad(substr($value, 0, -2), 2, 0, STR_PAD_LEFT).':'.str_pad(substr($value, -2), 2, 0, STR_PAD_LEFT);
				
				if (!$strip_meridiem && $meridiem) {
					$time = $time.' '.$meridiem;
				}
			}
			
			return $time;
		}
		
		function reload($msg = '', $uri = false)
		{
			$this->set_msg($msg);
			
			if ($uri) {
				header("Location: ".$this->conf->current_uri); 
			} else {
				header("Location: ".$this->conf->current_url); 
			}
			exit;
		}
		
		function redirect($url, $msg = '')
		{
			$this->set_msg($msg);
			header("Location: ".$url); exit;
		}
		
		function set_msg($msg)
		{
			if ($msg)
				$_SESSION['system_msg'][] = $msg;
		}
		
		function has_msg()
		{
			return !empty($_SESSION['system_msg']);
		}
		
		function get_msg()
		{
			if ($_SESSION['system_msg']) {
				$msg = "<ul class='system-msg'><li>".implode('</li><li>', $_SESSION['system_msg'])."</li></ul>";
				unset($_SESSION['system_msg']);
			} 
			
			return $msg;
		}	
		
		function get_list($type,$param = array())
		{
			$list = new DataList;
			
			switch ($type)
			{
				case 'states' :
					return $list->get_states();
					break;
					
				case 'canada' :
					return $list->get_canada_states();
					break;
					
				case 'months' :
					return $list->get_months();
					break;
			
				case 'year' :
					$start = $param[0] ? $param[0] : date('Y')-80;
					$end = $param[1] ? $param[1] : date('Y');
					return $list->get_years($start, $end);
					break;
				case 'week' :
					return $list->get_week();
					break;
					
				case 'countries' :
					return $list->get_countries();
					break;
			
			}
		}
		
		function href($url, $param='')
		{
			$http = rtrim($url, '/');
			
			if ($param) {
				$token = strpos($url, '?') ? '&' : '?';	
				
				#$http .= $this->conf->rewrite ? '?'.ltrim($param, '/?&') : '&'.ltrim($param, '/?&');
				$http = $http . $token .ltrim($param, '/?&');
			}
			
			return rtrim($http, '?&');
		}
		
		function download_csv($name,$data,$header=array())
		{
			header('Content-type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.$name.'"');		
		
			$stream = fopen('php://output', 'w');
			
			if ($header) {
				fputcsv($stream,$header);
			} else {
				$header = array_keys( reset( $data ) );
				fputcsv($stream,$header);
			}
			
			foreach ($data as $row)
			{
				fputcsv($stream,$row);
			}
			exit();
		}
		/*
		# functions from wordpress
		function make_url_clickable($matches) {
			$ret = '';
			$url = $matches[2];
		 
			if ( empty($url) )
				return $matches[0];
			if ( in_array(substr($url, -1), array('.', ',', ';', ':')) === true ) {
				$ret = substr($url, -1);
				$url = substr($url, 0, strlen($url)-1);
			}
			return $matches[1] . "<a href=\"$url\" target='_blank'>$url</a>" . $ret;
		}
		
		function make_email_clickable($matches) {
			$email = $matches[2] . '@' . $matches[3];
			return $matches[1] . "<a href=\"mailto:$email\">$email</a>";
		}	
			 		 
		function make_clickable($ret,$type='url') {
			$ret = ' ' . $ret;

			if ($type == 'url')
				$ret = preg_replace_callback('#([\s>])([\w]+?://[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', array($this, 'make_url_clickable'), $ret);
		 	else if ($type == 'email')
				$ret = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', array($this, 'make_email_clickable'), $ret);

			$ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);
			$ret = trim($ret);
			return $ret;
		}	*/
		
		function paginate($rows, $page=1, $per_page=100, $pages_per_chapter = 10)
		{      
			$num = 0; 
			$pages = array();
			
			$r['prev_chapter'] = 0;
			$r['next_chapter'] = 0;
			
			$r['prev'] = 0;
			$r['next'] = 0;
			$r['pages'] = 0;
			$r['num_pages'] = 0;
			$r['limit_start'] = 0;
			$r['limit_length'] = 0;
			$r['display_begin'] = 0;
			$r['display_end'] = 0;
		  
		  	if ($rows <= 0) return $r;
			
			if ($page <= 0) $page = 1;
			if ($per_page <= 0) $per_page = 100;
			          		  
			$total_pages = ceil($rows / $per_page); # 0 - x
			
			$current_chapter = floor(($page - 1) / $pages_per_chapter) + 1; # 1 - x
			$total_chapter = ceil( $total_pages / $pages_per_chapter);
			
			$prev_chapter = $current_chapter > 1 ? ((($current_chapter - 1) * $pages_per_chapter) - $pages_per_chapter) + 1 : 0;
			$next_chapter = $current_chapter < $total_chapter ? ((($current_chapter + 1) * $pages_per_chapter) - $pages_per_chapter) + 1 : 0;
			
			$page = $page < 1 ? 1 : $page; # protect against negative #s
			$page = $page > $total_pages ? $total_pages : $page; # previous page number - 0 - $total_pages
			
			
			if ($page > 1) {
				$previous_page = $page - 1; 
			}
			
			if ($page < $total_pages) {
				$next_page = $page + 1;
			}
			
			$first_page_of_chapter = floor(($page - 1) / $pages_per_chapter) * $pages_per_chapter + 1;
			
			
			while ($num < $pages_per_chapter && $rows)
			{
				if ( $num + $first_page_of_chapter > $total_pages ) break;
				
				$pages[$num + $first_page_of_chapter] = $num + $first_page_of_chapter;
				
				++$num;
			}
			
			$display_begin = $page ? ($page - 1) * $per_page + 1 : 0;
			$display_end = $page ? $display_begin + $per_page - 1 : 0;
			
			$r['prev_chapter'] = $prev_chapter;
			$r['next_chapter'] = $next_chapter;
			
			$r['current_page'] = $page;
			
			$r['prev'] = $previous_page; # page # of previous page
			$r['next'] = $next_page; # page # of next page
			$r['pages'] = $pages; # ie, pages, 1,2,3,4,5,6,7,8,9,10 in array
			$r['num_pages'] = $total_pages; # total pages possible
			$r['limit_start'] = $page ? ($page - 1) * $per_page : 0; # database LIMIT x, y -> x
			$r['limit_length'] = $per_page; # database LIMIT x, y -> y
			$r['display_begin'] = $display_begin; # ie: displaying items 10 - 21 of 100 -> 10
			$r['display_end'] = $display_end > $rows ? $rows : $display_end; # ie: displaying items 10 - 21 of 100 -> 21
			return $r;
		}
		
		function search($tbl, $string, $fields, $extra='') # $extra (string) for stuff like STATUS = 1
		{
			$string = $this->sql->sanitize($string);
			
			if ($tbl && $string && $fields) {
				foreach ((array) $fields as $key=>$v)
				{
					if ($key) {
						$or = 'OR';	
					}
					$where .= " $or `$v` LIKE '%$string%'";
				}
				
				if ($where) {
					$where = 'WHERE ('.$where.')';

					$this->sql->query("SELECT * FROM $tbl $where $extra");
					return $this->sql->fetch_all();
				}
			}
		}
		
		function is_unique($fields, $tbl, $and = '')
		{
			$fields = $this->sql->sanitize($fields);
			
			foreach ($fields as $name=>$val)
			{
				$w = "`$name` = '$val'";
				$where[] = $w;
			}
			
			if ($and) {
				$where[] = $and;	
			}
			
			if ($where) {
				$this->sql->query("SELECT COUNT(*) FROM $tbl WHERE ".implode(' AND ', $where));
				return $this->sql->result();
			}
		}
		
		function cache($id, $data = NULL, $expire = 86400)
		{
			static $cached_data = array();
			
			if ($data !== NULL) {
				$this->sql->query("REPLACE INTO 	".$this->conf->CACHE." SET host='".$_SERVER['HTTP_HOST']."', cache_token = '".$this->sql->sanitize($id)."', data = '".$this->sql->sanitize(serialize($data))."', expire = '".(time()+$expire)."'");
				$cached_data[$_SERVER['HTTP_HOST']][$id] = $data;
			} else {	
				if (!$cached_data[$_SERVER['HTTP_HOST']][$id]) {
					$this->sql->query("SELECT data, expire FROM ".$this->conf->CACHE." WHERE host='".$_SERVER['HTTP_HOST']."' AND cache_token = '".$this->sql->sanitize($id)."' AND expire > ".time());
					$data = $this->sql->fetch();
					$cached_data[$_SERVER['HTTP_HOST']][$id] = unserialize($data['data']);
				} 

				return $cached_data[$_SERVER['HTTP_HOST']][$id] ? $cached_data[$_SERVER['HTTP_HOST']][$id] : NULL;
			}
		}
		
		function copy_delete_data($tbl, $data)
		{
			if ($tbl && $data) {
				$serialized = addslashes(gzcompress(serialize($data)));
				$this->sql->query("INSERT INTO ".$this->conf->DELETE_ARCHIVE." (source_table, data, delete_dt) VALUES ('$tbl', '$serialized', NOW())");
			}
		}
		
		function base64_encode($string)
		{
			return strtr(base64_encode($string), '+/=', '-_,');	
		}
		
		function base64_decode($string)
		{
			return base64_decode(strtr($string, '-_,', '+/='));	
		}
	}